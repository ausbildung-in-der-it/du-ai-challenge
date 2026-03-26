import { ref } from 'vue';

/**
 * Manual SSE stream reader for Vercel AI SDK data protocol.
 * Unlike useCompletion, the URL is passed at call time (not init),
 * so it works with dynamic session IDs.
 */
export function useStreamText() {
    const text = ref('');
    const isLoading = ref(false);
    const error = ref<string | null>(null);

    function getXsrfToken(): string {
        return (
            document.cookie
                .split('; ')
                .find((row) => row.startsWith('XSRF-TOKEN='))
                ?.split('=')[1]
                ?.replace(/%3D/g, '=') ?? ''
        );
    }

    async function stream(url: string, body: Record<string, unknown>): Promise<string> {
        text.value = '';
        error.value = null;
        isLoading.value = true;

        try {
            const res = await fetch(url, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'text/event-stream',
                    'X-XSRF-TOKEN': getXsrfToken(),
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify(body),
            });

            if (!res.ok) {
                throw new Error(`Stream failed: ${res.status}`);
            }

            const reader = res.body?.getReader();
            if (!reader) throw new Error('No reader');

            const decoder = new TextDecoder();
            let buffer = '';

            while (true) {
                const { done, value } = await reader.read();
                if (done) break;

                buffer += decoder.decode(value, { stream: true });
                const lines = buffer.split('\n');
                buffer = lines.pop() ?? '';

                for (const line of lines) {
                    // Vercel data protocol: 0:"text chunk"
                    if (line.startsWith('0:')) {
                        try {
                            const chunk = JSON.parse(line.slice(2));
                            if (typeof chunk === 'string') {
                                text.value += chunk;
                            }
                        } catch {
                            // not JSON, skip
                        }
                    }
                    // SSE format: data: {"event":"text_delta","data":"chunk"}
                    else if (line.startsWith('data: ') && !line.includes('[DONE]')) {
                        try {
                            const event = JSON.parse(line.slice(6));
                            if (event.event === 'text_delta' && event.data) {
                                text.value += event.data;
                            }
                        } catch {
                            // not JSON event, skip
                        }
                    }
                }
            }
        } catch (e) {
            error.value = e instanceof Error ? e.message : 'Stream error';
        } finally {
            isLoading.value = false;
        }

        return text.value;
    }

    function reset() {
        text.value = '';
        error.value = null;
        isLoading.value = false;
    }

    return { text, isLoading, error, stream, reset };
}
