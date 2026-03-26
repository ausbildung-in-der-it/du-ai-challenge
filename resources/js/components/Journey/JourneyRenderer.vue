<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { ArrowRight, RotateCcw, Zap } from 'lucide-vue-next';
import QuizCard from './QuizCard.vue';
import LearnCard from './LearnCard.vue';
import PersonaInterstitial from './PersonaInterstitial.vue';
import PromptReveal from './PromptReveal.vue';
import { useJourneySession } from '@/composables/useJourneySession';
import { useCompletion } from '@ai-sdk/vue';
import { useTypewriter } from '@/composables/useTypewriter';

const props = defineProps<{
    journey: App.Data.LearningJourneyData;
}>();

type Screen = 'loading' | 'start' | 'journey' | 'persona' | 'prompt-reveal' | 'end';

const screen = ref<Screen>('loading');
const session = useJourneySession(props.journey);

const currentBlock = computed(() => props.journey.blocks[session.currentBlock.value]);

// For the persona styled commentary on PromptReveal
function getXsrfToken(): string {
    return document.cookie
        .split('; ')
        .find((row) => row.startsWith('XSRF-TOKEN='))
        ?.split('=')[1]
        ?.replace(/%3D/g, '=') ?? '';
}

const styledCompletion = useCompletion({
    api: computed(() =>
        session.sessionId.value
            ? `/api/sessions/${session.sessionId.value}/commentaries`
            : '/api/sessions/_/commentaries',
    ).value,
    headers: () => ({
        'X-XSRF-TOKEN': getXsrfToken(),
        'X-Requested-With': 'XMLHttpRequest',
    }),
});

const promptText = ref<string | null>(null);
const promptTypewriter = useTypewriter(8);

// "Weiter" on quiz: always show (streaming handles its own loading state)
const showWeiterOnQuiz = computed(() => {
    // After first quiz, before persona shown → show Weiter (goes to persona)
    // Otherwise → always show (commentary streams independently)
    return true;
});

onMounted(async () => {
    const resumed = await session.resumeSession();
    screen.value = resumed ? 'journey' : 'start';
});

async function start() {
    await session.startSession();
    screen.value = 'journey';
}

function restart() {
    localStorage.removeItem(`du-journey-session-${props.journey.slug}`);
    screen.value = 'start';
}

async function onQuizAnswered(userSaidReal: boolean) {
    const block = currentBlock.value;
    if (!block || block.type !== 'quiz') return;

    const card = block.quiz_cards[session.currentItem.value];
    session.recordAnswer(card.id, userSaidReal, card.is_real);
    await session.saveProgress();
}

function onQuizNext() {
    // After first quiz and commentary done → show persona interstitial
    if (session.answeredQuizCount.value === 1 && !session.personaPromptShown.value) {
        screen.value = 'persona';
        return;
    }
    advanceToNext();
}

async function onPersonaSet(style: string) {
    await session.setPersona(style);

    // Fetch the prompt that WILL be used
    const block = currentBlock.value;
    if (block?.type === 'quiz' && session.sessionId.value) {
        const card = block.quiz_cards[session.currentItem.value];

        // Stream styled commentary
        styledCompletion.complete('Kommentiere diese Quiz-Antwort.', {
            body: {
                quiz_card_id: card.id,
                persona_style: style,
            },
        });

        // Fetch prompt from latest commentary
        setTimeout(async () => {
            try {
                const res = await fetch(
                    `/api/sessions/${session.sessionId.value}/cards/${card.id}/commentary`,
                    {
                        headers: {
                            Accept: 'application/json',
                            'X-XSRF-TOKEN': getXsrfToken(),
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    },
                );
                if (res.ok) {
                    const data = await res.json();
                    promptText.value = data.prompt_used;
                    if (data.prompt_used) promptTypewriter.start(data.prompt_used);
                }
            } catch { /* prompt display is nice-to-have */ }
        }, 1000);
    }

    screen.value = 'prompt-reveal';
}

function onPersonaSkip() {
    session.personaPromptShown.value = true;
    advanceToNext();
}

function onPromptRevealNext() {
    promptText.value = null;
    advanceToNext();
}

async function advanceToNext() {
    const result = await session.advance();
    if (result === 'end') {
        screen.value = 'end';
    } else {
        screen.value = 'journey';
    }
}

const endTitle = computed(() => {
    if (session.totalQuizCards.value === 0) return 'Geschafft!';
    const pct = session.correctCount.value / session.totalQuizCards.value;
    if (pct >= 0.9) return 'KI-Experte!';
    if (pct >= 0.7) return 'Gut informiert!';
    if (pct >= 0.5) return 'Nicht schlecht!';
    return 'Willkommen in 2026.';
});

const endMessage = computed(() => {
    if (session.totalQuizCards.value === 0) return 'Du hast die Lernreise abgeschlossen.';
    const pct = session.correctCount.value / session.totalQuizCards.value;
    if (pct >= 0.9) return 'Beeindruckend. Du erkennst die Realität besser als die meisten KI-Modelle.';
    if (pct >= 0.7) return 'Du hast ein solides Gespür dafür, was in der KI-Welt möglich ist — und was nicht.';
    if (pct >= 0.5) return 'Die Grenze zwischen Realität und Fiktion verschwimmt. Genau das macht KI so faszinierend — und gefährlich.';
    return 'Die Realität ist absurder als jede Fiktion. Genau der Punkt: Wir müssen lernen, KI-Geschichten kritisch einzuordnen.';
});
</script>

<template>
    <div class="flex h-dvh flex-col bg-[#f5f5f7] font-sans dark:bg-black">
        <!-- LOADING -->
        <div v-if="screen === 'loading'" class="flex flex-1 items-center justify-center">
            <div class="h-6 w-6 animate-spin rounded-full border-2 border-[#007aff] border-t-transparent" />
        </div>

        <!-- START -->
        <div v-else-if="screen === 'start'" class="flex flex-1 flex-col items-center justify-center px-8 text-center">
            <div class="mb-8 flex h-20 w-20 items-center justify-center rounded-[22px] bg-gradient-to-b from-[#007aff] to-[#0055d4] shadow-lg shadow-[#007aff]/25">
                <Zap class="h-10 w-10 text-white" />
            </div>
            <h1 class="mb-3 text-[34px] leading-[1.1] font-bold tracking-[-1px] text-[#1d1d1f] dark:text-[#f5f5f7]">
                {{ journey.title }}
            </h1>
            <p v-if="journey.description" class="mb-2 max-w-[300px] text-[17px] leading-[1.5] text-[#86868b] dark:text-[#98989d]">
                {{ journey.description }}
            </p>
            <p class="mb-10 text-[13px] text-[#86868b]/60 dark:text-[#98989d]/60">
                {{ session.totalSteps.value }} Schritte
            </p>
            <button
                class="flex cursor-pointer items-center gap-2.5 rounded-full bg-[#007aff] px-10 py-4 text-[17px] font-semibold tracking-[-0.2px] text-white shadow-lg shadow-[#007aff]/25 transition-all active:scale-[0.97]"
                @click="start"
            >
                Los geht's
            </button>
        </div>

        <!-- JOURNEY -->
        <template v-else-if="screen === 'journey' && currentBlock">
            <header class="flex shrink-0 items-center justify-between px-5 pt-4 pb-3">
                <span class="text-[17px] font-semibold tracking-[-0.2px] text-[#1d1d1f] dark:text-[#f5f5f7]">
                    {{ journey.title }}
                </span>
                <span class="text-[15px] font-medium text-[#86868b] dark:text-[#98989d]">
                    {{ session.currentStep.value + 1 }} / {{ session.totalSteps.value }}
                </span>
            </header>

            <div class="mx-5 mb-4 h-[3px] overflow-hidden rounded-full bg-black/[0.06] dark:bg-white/[0.08]">
                <div class="h-full rounded-full bg-[#007aff] transition-all duration-500 ease-out" :style="{ width: session.progress.value + '%' }" />
            </div>

            <Transition
                mode="out-in"
                enter-active-class="transition-all duration-300 ease-out"
                enter-from-class="opacity-0 translate-y-4"
                enter-to-class="opacity-100 translate-y-0"
                leave-active-class="transition-all duration-200 ease-in"
                leave-from-class="opacity-100 translate-y-0"
                leave-to-class="opacity-0 -translate-y-4"
            >
                <QuizCard
                    v-if="currentBlock.type === 'quiz'"
                    :key="`quiz-${session.currentBlock.value}-${session.currentItem.value}`"
                    :card="currentBlock.quiz_cards[session.currentItem.value]"
                    :session-id="session.sessionId.value"
                    :show-weiter-button="showWeiterOnQuiz"
                    @answered="onQuizAnswered"
                    @next="onQuizNext"
                />
                <LearnCard
                    v-else-if="currentBlock.type === 'learn'"
                    :key="`learn-${session.currentBlock.value}-${session.currentItem.value}`"
                    :card="currentBlock.learn_cards[session.currentItem.value]"
                    @next="advanceToNext"
                />
            </Transition>
        </template>

        <!-- PERSONA -->
        <template v-else-if="screen === 'persona'">
            <header class="flex shrink-0 items-center justify-between px-5 pt-4 pb-3">
                <span class="text-[17px] font-semibold tracking-[-0.2px] text-[#1d1d1f] dark:text-[#f5f5f7]">
                    Prompt Engineering
                </span>
            </header>
            <PersonaInterstitial @set-persona="onPersonaSet" @skip="onPersonaSkip" />
        </template>

        <!-- PROMPT REVEAL -->
        <template v-else-if="screen === 'prompt-reveal'">
            <header class="flex shrink-0 items-center justify-between px-5 pt-4 pb-3">
                <span class="text-[17px] font-semibold tracking-[-0.2px] text-[#1d1d1f] dark:text-[#f5f5f7]">
                    Lernmoment
                </span>
            </header>
            <PromptReveal
                :prompt-used="promptText"
                :commentary-text="styledCompletion.completion.value"
                :is-loading="styledCompletion.isLoading.value"
                @next="onPromptRevealNext"
            />
        </template>

        <!-- END -->
        <div v-else-if="screen === 'end'" class="flex flex-1 flex-col items-center justify-center px-8 text-center">
            <div v-if="session.totalQuizCards.value > 0" class="mb-6 flex h-36 w-36 flex-col items-center justify-center rounded-full border-[6px] border-[#007aff]">
                <span class="text-[42px] leading-none font-extrabold text-[#007aff]">
                    {{ session.correctCount.value }}/{{ session.totalQuizCards.value }}
                </span>
                <span class="mt-0.5 text-[13px] text-[#86868b] dark:text-[#98989d]">richtig</span>
            </div>
            <div v-else class="mb-6 text-6xl">🎉</div>

            <h1 class="mb-3 text-[32px] leading-[1.15] font-extrabold tracking-[-0.8px] text-[#1d1d1f] dark:text-[#f5f5f7]">
                {{ endTitle }}
            </h1>
            <p class="mb-8 max-w-[300px] text-[17px] leading-[1.5] text-[#86868b] dark:text-[#98989d]">
                {{ endMessage }}
            </p>
            <button
                class="flex cursor-pointer items-center gap-2 rounded-full bg-[#007aff] px-10 py-4 text-[17px] font-semibold tracking-[-0.2px] text-white shadow-lg shadow-[#007aff]/25 transition-all active:scale-[0.97]"
                @click="restart"
            >
                <RotateCcw class="h-5 w-5" />
                Nochmal spielen
            </button>

            <a
                href="/ai-ready"
                class="mt-4 flex items-center gap-2 text-[15px] font-medium text-[#007aff] transition-colors hover:text-[#007aff]/80"
            >
                AI Ready: Das komplette Training für dein Team
                <ArrowRight class="h-4 w-4" />
            </a>
        </div>
    </div>
</template>
