import { ref } from 'vue';

/**
 * Manual SSE stream reader for Laravel AI SDK format.
 *
 * The actual wire format from Laravel AI SDK (non-Vercel) is:
 *   data: {"type":"text-delta","id":"...","delta":"chunk"}
 *   data: {"type":"finish"}
 *   data: [DONE]
 *
 * Vercel data protocol (when usingVercelDataProtocol):
 *   0:"chunk"
 *   e:{"finishReason":"stop"}
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

    async function stream(
        url: string,
        body: Record<string, unknown>,
    ): Promise<string> {
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
                    const trimmed = line.trim();
                    if (!trimmed) continue;

                    // Vercel protocol: 0:"chunk"
                    if (trimmed.startsWith('0:')) {
                        try {
                            const chunk = JSON.parse(trimmed.slice(2));
                            if (typeof chunk === 'string') {
                                text.value += chunk;
                            }
                        } catch {
                            /* skip */
                        }
                        continue;
                    }

                    // Laravel AI SDK SSE: data: {"type":"text-delta","delta":"chunk"}
                    if (trimmed.startsWith('data: ')) {
                        const payload = trimmed.slice(6);
                        if (payload === '[DONE]') continue;

                        try {
                            const event = JSON.parse(payload);

                            // Laravel AI SDK format
                            if (event.type === 'text-delta' && event.delta) {
                                text.value += event.delta;
                            }
                            // Alternative: plain event format
                            else if (
                                event.event === 'text_delta' &&
                                event.data
                            ) {
                                text.value += event.data;
                            }
                        } catch {
                            /* skip non-JSON lines */
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
