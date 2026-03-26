import { ref, computed } from 'vue';
import type { LearningJourneyData } from '@/composables/types';

export type QuizAnswer = {
    user_said_real: boolean;
    correct: boolean;
    commentary_id?: string;
};

export type SessionState = {
    sessionId: string;
    currentBlock: number;
    currentItem: number;
    answers: Record<string, QuizAnswer>;
    personaStyle: string | null;
    personaPromptShown: boolean;
};

const STORAGE_KEY = 'du-journey-session';

function getXsrfToken(): string {
    return (
        document.cookie
            .split('; ')
            .find((row) => row.startsWith('XSRF-TOKEN='))
            ?.split('=')[1]
            ?.replace(/%3D/g, '=') ?? ''
    );
}

async function api<T>(url: string, options: RequestInit = {}): Promise<T> {
    const res = await fetch(url, {
        ...options,
        credentials: 'same-origin',
        headers: {
            'Content-Type': 'application/json',
            Accept: 'application/json',
            'X-XSRF-TOKEN': getXsrfToken(),
            'X-Requested-With': 'XMLHttpRequest',
            ...((options.headers as Record<string, string>) ?? {}),
        },
    });
    if (!res.ok) throw new Error(`API error: ${res.status}`);
    return res.json();
}

export function useJourneySession(journey: App.Data.LearningJourneyData) {
    const sessionId = ref<string | null>(null);
    const currentBlock = ref(0);
    const currentItem = ref(0);
    const answers = ref<Record<string, QuizAnswer>>({});
    const personaStyle = ref<string | null>(null);
    const personaPromptShown = ref(false);
    const loading = ref(false);

    function blockItemCount(block: App.Data.JourneyBlockData): number {
        if (block.type === 'quiz') return block.quiz_cards.length;
        if (block.type === 'learn') return block.learn_cards.length;
        return 1; // compare, prompt, or any custom block = 1 step
    }

    const totalSteps = computed(() =>
        journey.blocks.reduce((sum, block) => sum + blockItemCount(block), 0),
    );

    const currentStep = computed(() => {
        let step = 0;
        for (let i = 0; i < currentBlock.value; i++) {
            step += blockItemCount(journey.blocks[i]);
        }
        return step + currentItem.value;
    });

    const progress = computed(() =>
        totalSteps.value === 0
            ? 0
            : (currentStep.value / totalSteps.value) * 100,
    );

    const totalQuizCards = computed(() =>
        journey.blocks.reduce(
            (sum, b) =>
                b.type === 'quiz' ? sum + b.quiz_cards.length : sum,
            0,
        ),
    );

    const correctCount = computed(
        () => Object.values(answers.value).filter((a) => a.correct).length,
    );

    const answeredQuizCount = computed(
        () => Object.keys(answers.value).length,
    );

    // Persist session ID to localStorage
    function saveToStorage() {
        if (sessionId.value) {
            localStorage.setItem(
                `${STORAGE_KEY}-${journey.slug}`,
                sessionId.value,
            );
        }
    }

    function loadFromStorage(): string | null {
        return localStorage.getItem(`${STORAGE_KEY}-${journey.slug}`);
    }

    async function startSession(): Promise<string> {
        const data = await api<{ session_id: string }>('/api/sessions', {
            method: 'POST',
            body: JSON.stringify({
                learning_journey_id: journey.id,
            }),
        });
        sessionId.value = data.session_id;
        currentBlock.value = 0;
        currentItem.value = 0;
        answers.value = {};
        personaStyle.value = null;
        personaPromptShown.value = false;
        saveToStorage();
        return data.session_id;
    }

    async function resumeSession(): Promise<boolean> {
        const stored = loadFromStorage();
        if (!stored) return false;

        try {
            const data = await api<{
                session_id: string;
                current_block: number;
                current_item: number;
                answers: Record<string, QuizAnswer>;
                persona_style: string | null;
                persona_prompt_shown: boolean;
                completed: boolean;
            }>(`/api/sessions/${stored}`);

            if (data.completed) return false;

            sessionId.value = data.session_id;
            currentBlock.value = data.current_block;
            currentItem.value = data.current_item;
            answers.value = data.answers ?? {};
            personaStyle.value = data.persona_style;
            personaPromptShown.value = data.persona_prompt_shown;
            return true;
        } catch {
            localStorage.removeItem(`${STORAGE_KEY}-${journey.slug}`);
            return false;
        }
    }

    async function saveProgress() {
        if (!sessionId.value) return;
        await api(`/api/sessions/${sessionId.value}`, {
            method: 'PATCH',
            body: JSON.stringify({
                current_block: currentBlock.value,
                current_item: currentItem.value,
                answers: answers.value,
            }),
        });
    }

    function recordAnswer(cardId: number, userSaidReal: boolean, isReal: boolean) {
        answers.value[String(cardId)] = {
            user_said_real: userSaidReal,
            correct: userSaidReal === isReal,
        };
    }

    async function advance() {
        const block = journey.blocks[currentBlock.value];
        if (!block) return 'end';

        // Single-step blocks (compare, etc.) → always go to next block
        const items =
            block.type === 'quiz'
                ? block.quiz_cards
                : block.type === 'learn'
                  ? block.learn_cards
                  : [null]; // compare/custom = 1 virtual item

        if (currentItem.value < items.length - 1) {
            currentItem.value++;
        } else if (currentBlock.value < journey.blocks.length - 1) {
            currentBlock.value++;
            currentItem.value = 0;
        } else {
            await saveProgress();
            return 'end';
        }

        await saveProgress();
        return 'continue';
    }

    async function setPersona(style: string) {
        if (!sessionId.value) return;
        personaStyle.value = style;
        personaPromptShown.value = true;
        await api(`/api/sessions/${sessionId.value}/persona`, {
            method: 'POST',
            body: JSON.stringify({ persona_style: style }),
        });
    }

    return {
        sessionId,
        currentBlock,
        currentItem,
        answers,
        personaStyle,
        personaPromptShown,
        loading,
        totalSteps,
        currentStep,
        progress,
        totalQuizCards,
        correctCount,
        answeredQuizCount,
        startSession,
        resumeSession,
        saveProgress,
        recordAnswer,
        advance,
        setPersona,
    };
}
