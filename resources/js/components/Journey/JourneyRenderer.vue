<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { ArrowRight, RotateCcw, Zap } from 'lucide-vue-next';
import QuizCard from './QuizCard.vue';
import LearnCard from './LearnCard.vue';
import AiCompareBlock from './AiCompareBlock.vue';
import ChoiceCard from './ChoiceCard.vue';
import SpeechBlock from './SpeechBlock.vue';
import TimelineCard from './TimelineCard.vue';
import PersonaInterstitial from './PersonaInterstitial.vue';
import PromptReveal from './PromptReveal.vue';
import { useJourneySession } from '@/composables/useJourneySession';
import { useStreamText } from '@/composables/useStreamText';
import posthog from 'posthog-js';

const props = defineProps<{
    journey: App.Data.LearningJourneyData;
}>();

type Screen = 'loading' | 'start' | 'journey' | 'persona' | 'prompt-reveal' | 'end';

const screen = ref<Screen>('loading');
const session = useJourneySession(props.journey);
const styledCommentary = useStreamText();

const currentBlock = computed(() => props.journey.blocks[session.currentBlock.value]);

// Store the first commentary text for PersonaInterstitial
const lastCommentaryText = ref<string | null>(null);
const promptText = ref<string | null>(null);

function getXsrfToken(): string {
    return document.cookie
        .split('; ')
        .find((row) => row.startsWith('XSRF-TOKEN='))
        ?.split('=')[1]
        ?.replace(/%3D/g, '=') ?? '';
}

onMounted(async () => {
    const resumed = await session.resumeSession();
    screen.value = resumed ? 'journey' : 'start';
});

async function start() {
    await session.startSession();
    screen.value = 'journey';
    posthog.capture('journey_started', { journey_slug: props.journey.slug, journey_title: props.journey.title });
}

function restart() {
    localStorage.removeItem(`du-journey-session-${props.journey.slug}`);
    screen.value = 'start';
    posthog.capture('journey_restarted', { journey_slug: props.journey.slug, journey_title: props.journey.title });
}

async function onQuizAnswered(userSaidReal: boolean) {
    const block = currentBlock.value;
    if (!block || block.type !== 'quiz') return;

    const card = block.quiz_cards[session.currentItem.value];
    session.recordAnswer(card.id, userSaidReal, card.is_real);
    await session.saveProgress();
}

function onCommentaryDone(text: string) {
    lastCommentaryText.value = text;
}

function onQuizNext() {
    if (session.answeredQuizCount.value === 1 && !session.personaPromptShown.value) {
        screen.value = 'persona';
        return;
    }
    advanceToNext();
}

async function onPersonaSet(style: string) {
    await session.setPersona(style);
    posthog.capture('persona_selected', { persona_style: style, journey_slug: props.journey.slug });

    const block = currentBlock.value;
    if (block?.type === 'quiz' && session.sessionId.value) {
        const card = block.quiz_cards[session.currentItem.value];

        // Stream styled commentary (dynamic URL — no stale session bug)
        styledCommentary.stream(
            `/api/sessions/${session.sessionId.value}/commentaries`,
            { quiz_card_id: card.id, persona_style: style },
        );

        // Fetch the prompt after a short delay (needs DB write from stream's then() callback)
        fetchPrompt(card.id);
    }

    screen.value = 'prompt-reveal';
}

async function fetchPrompt(cardId: number, retries = 5) {
    for (let i = 0; i < retries; i++) {
        await new Promise((r) => setTimeout(r, 1500));
        try {
            const res = await fetch(
                `/api/sessions/${session.sessionId.value}/cards/${cardId}/commentary`,
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
                if (data.prompt_used) {
                    promptText.value = data.prompt_used;
                    return;
                }
            }
        } catch { /* retry */ }
    }
}

function onPersonaSkip() {
    session.personaPromptShown.value = true;
    advanceToNext();
}

function onPromptRevealNext() {
    promptText.value = null;
    styledCommentary.reset();
    advanceToNext();
}

async function advanceToNext() {
    const result = await session.advance();
    if (result === 'end') {
        screen.value = 'end';
        posthog.capture('journey_completed', {
            journey_slug: props.journey.slug,
            journey_title: props.journey.title,
            correct_count: session.correctCount.value,
            total_quiz_cards: session.totalQuizCards.value,
        });
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
                    :show-weiter-button="true"
                    @answered="onQuizAnswered"
                    @commentary-done="onCommentaryDone"
                    @next="onQuizNext"
                />
                <LearnCard
                    v-else-if="currentBlock.type === 'learn'"
                    :key="`learn-${session.currentBlock.value}-${session.currentItem.value}`"
                    :card="currentBlock.learn_cards[session.currentItem.value]"
                    @next="advanceToNext"
                />
                <AiCompareBlock
                    v-else-if="currentBlock.type === 'compare'"
                    :key="`compare-${session.currentBlock.value}`"
                    :quiz-card-id="currentBlock.config?.quiz_card_id ?? 0"
                    :headline="currentBlock.config?.headline ?? ''"
                    :is-real="currentBlock.config?.is_real ?? true"
                    @next="advanceToNext"
                />
                <ChoiceCard
                    v-else-if="currentBlock.type === 'choice'"
                    :key="`choice-${session.currentBlock.value}-${session.currentItem.value}`"
                    :card="currentBlock.choice_cards[session.currentItem.value]"
                    @next="advanceToNext"
                />
                <SpeechBlock
                    v-else-if="currentBlock.type === 'speech'"
                    :key="`speech-${session.currentBlock.value}`"
                    @next="advanceToNext"
                />
                <TimelineCard
                    v-else-if="currentBlock.type === 'timeline'"
                    :key="`timeline-${session.currentBlock.value}`"
                    :config="currentBlock.config"
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
            <PersonaInterstitial
                :previous-commentary="lastCommentaryText"
                @set-persona="onPersonaSet"
                @skip="onPersonaSkip"
            />
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
                :commentary-text="styledCommentary.text.value"
                :commentary-loading="styledCommentary.isLoading.value"
                @next="onPromptRevealNext"
            />
        </template>

        <!-- END -->
        <div v-else-if="screen === 'end'" class="flex flex-1 flex-col overflow-y-auto px-5 pt-8 pb-8">
            <!-- Score -->
            <div class="mb-6 flex flex-col items-center text-center">
                <div v-if="session.totalQuizCards.value > 0" class="mb-5 flex h-32 w-32 flex-col items-center justify-center rounded-full border-[5px] border-[#007aff]">
                    <span class="text-[36px] leading-none font-extrabold text-[#007aff]">
                        {{ session.correctCount.value }}/{{ session.totalQuizCards.value }}
                    </span>
                    <span class="mt-0.5 text-[12px] text-[#86868b] dark:text-[#98989d]">richtig</span>
                </div>

                <h1 class="mb-2 text-[28px] leading-[1.15] font-extrabold tracking-[-0.6px] text-[#1d1d1f] dark:text-[#f5f5f7]">
                    {{ endTitle }}
                </h1>
                <p class="mb-6 max-w-[300px] text-[15px] leading-[1.5] text-[#86868b] dark:text-[#98989d]">
                    {{ endMessage }}
                </p>
            </div>

            <!-- AI Ready CTA Card -->
            <div class="mb-4 overflow-hidden rounded-2xl bg-white ring-1 ring-black/[0.04] dark:bg-[#1c1c1e] dark:ring-white/[0.06]">
                <div class="p-6">
                    <p class="mb-1 text-[11px] font-bold tracking-wider text-[#007aff] uppercase">
                        Hands-on KI-Training
                    </p>
                    <h3 class="mb-2 text-[22px] leading-[1.2] font-bold tracking-[-0.4px] text-[#1d1d1f] dark:text-[#f5f5f7]">
                        Dein Team soll eigene KI-Agenten bauen?
                    </h3>
                    <p class="mb-5 text-[15px] leading-[1.5] text-[#86868b] dark:text-[#98989d]">
                        In unserem AI Ready Training lernt dein Team hands-on, KI-Agenten zu bauen, Chancen und Grenzen einzuschätzen und KI strategisch einzusetzen — mit genau solchen interaktiven Formaten wie diesem Quiz.
                    </p>
                    <a
                        href="/ai-ready"
                        class="flex w-full items-center justify-center gap-2 rounded-xl bg-[#1d1d1f] px-5 py-3.5 text-[17px] font-semibold tracking-[-0.2px] text-white transition-all active:scale-[0.97] dark:bg-[#f5f5f7] dark:text-[#1d1d1f]"
                        @click="posthog.capture('ai_ready_cta_clicked', { source: 'journey_end', journey_slug: journey.slug })"
                    >
                        Mehr erfahren
                        <ArrowRight class="h-4.5 w-4.5" />
                    </a>
                </div>
            </div>

            <!-- Nochmal spielen (secondary) -->
            <button
                class="flex w-full cursor-pointer items-center justify-center gap-2 rounded-2xl px-4 py-3 text-[15px] font-medium text-[#86868b] transition-all active:scale-[0.97] dark:text-[#98989d]"
                @click="restart"
            >
                <RotateCcw class="h-4 w-4" />
                Nochmal spielen
            </button>
        </div>
    </div>
</template>
