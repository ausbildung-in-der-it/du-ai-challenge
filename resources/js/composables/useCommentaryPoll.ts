import { ref } from 'vue';

function getXsrfToken(): string {
    return (
        document.cookie
            .split('; ')
            .find((row) => row.startsWith('XSRF-TOKEN='))
            ?.split('=')[1]
            ?.replace(/%3D/g, '=') ?? ''
    );
}

export function useCommentaryPoll() {
    const text = ref<string | null>(null);
    const promptUsed = ref<string | null>(null);
    const isLoading = ref(false);
    const commentaryId = ref<string | null>(null);

    let pollTimer: ReturnType<typeof setInterval> | null = null;
    let pollCount = 0;
    const MAX_POLLS = 30; // 30 * 1500ms = 45 seconds

    function stopPolling() {
        if (pollTimer) {
            clearInterval(pollTimer);
            pollTimer = null;
        }
    }

    async function requestCommentary(
        sessionId: string,
        quizCardId: number,
        personaStyle?: string | null,
    ): Promise<void> {
        reset();
        isLoading.value = true;

        try {
            const res = await fetch(`/api/sessions/${sessionId}/commentaries`, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-XSRF-TOKEN': getXsrfToken(),
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({
                    quiz_card_id: quizCardId,
                    persona_style: personaStyle ?? undefined,
                }),
            });

            if (!res.ok) throw new Error(`POST failed: ${res.status}`);

            const data = await res.json();
            commentaryId.value = data.commentary_id;

            // Start polling
            pollCount = 0;
            pollTimer = setInterval(() => poll(sessionId), 1500);
        } catch {
            isLoading.value = false;
            text.value = 'KI-Kommentar konnte nicht geladen werden.';
        }
    }

    async function poll(sessionId: string) {
        if (!commentaryId.value) return;
        pollCount++;

        if (pollCount > MAX_POLLS) {
            stopPolling();
            isLoading.value = false;
            text.value = 'KI-Kommentar hat zu lange gedauert.';
            return;
        }

        try {
            const res = await fetch(
                `/api/sessions/${sessionId}/commentaries/${commentaryId.value}`,
                {
                    credentials: 'same-origin',
                    headers: {
                        Accept: 'application/json',
                        'X-XSRF-TOKEN': getXsrfToken(),
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                },
            );

            if (!res.ok) return;
            const data = await res.json();

            if (data.status === 'done' || data.status === 'failed') {
                stopPolling();
                text.value = data.text;
                promptUsed.value = data.prompt_used;
                isLoading.value = false;
            }
        } catch {
            // Silently retry on next poll
        }
    }

    function reset() {
        stopPolling();
        text.value = null;
        promptUsed.value = null;
        commentaryId.value = null;
        isLoading.value = false;
        pollCount = 0;
    }

    return {
        text,
        promptUsed,
        isLoading,
        commentaryId,
        requestCommentary,
        reset,
    };
}
