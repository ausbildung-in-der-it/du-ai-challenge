<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import {
    ArrowRight,
    CircleHelp,
    Gift,
    RotateCcw,
    Sparkles,
    Zap,
} from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';
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
import { index as aiReadyIndex } from '@/actions/App/Http/Controllers/AiReadyController';
import posthog from 'posthog-js';
import NewsletterLeadForm from '@/components/NewsletterLeadForm.vue';
import TrainingGiveawayModal from '@/components/TrainingGiveawayModal.vue';

const props = defineProps<{
    journey: App.Data.LearningJourneyData;
}>();

type Screen =
    | 'loading'
    | 'start'
    | 'journey'
    | 'persona'
    | 'prompt-reveal'
    | 'end';

const screen = ref<Screen>('loading');
const session = useJourneySession(props.journey);
const styledCommentary = useStreamText();
const isGiveawayModalOpen = ref(false);

const currentBlock = computed(
    () => props.journey.blocks[session.currentBlock.value],
);

type TimelineConfig = {
    title: string;
    subtitle: string;
    teaser_headline: string;
    teaser_text: string;
    teaser_cta?: boolean;
};

// Store the first commentary text for PersonaInterstitial
const lastCommentaryText = ref<string | null>(null);
const promptText = ref<string | null>(null);

function getTimelineConfig(
    config: Record<string, unknown> | null,
): TimelineConfig {
    return {
        title: typeof config?.title === 'string' ? config.title : '',
        subtitle: typeof config?.subtitle === 'string' ? config.subtitle : '',
        teaser_headline:
            typeof config?.teaser_headline === 'string'
                ? config.teaser_headline
                : '',
        teaser_text:
            typeof config?.teaser_text === 'string' ? config.teaser_text : '',
        teaser_cta:
            typeof config?.teaser_cta === 'boolean'
                ? config.teaser_cta
                : undefined,
    };
}

function getXsrfToken(): string {
    return (
        document.cookie
            .split('; ')
            .find((row) => row.startsWith('XSRF-TOKEN='))
            ?.split('=')[1]
            ?.replace(/%3D/g, '=') ?? ''
    );
}

onMounted(async () => {
    const resumed = await session.resumeSession();
    screen.value = resumed ? 'journey' : 'start';

    // Debug: ?step=N jumps directly to a step
    const stepParam = new URLSearchParams(window.location.search).get('step');
    if (stepParam !== null) {
        let remaining = parseInt(stepParam, 10);
        if (!isNaN(remaining) && remaining >= 0) {
            for (let b = 0; b < props.journey.blocks.length; b++) {
                const block = props.journey.blocks[b];
                const count = block.type === 'quiz' ? block.quiz_cards.length
                    : block.type === 'learn' ? block.learn_cards.length
                    : block.type === 'choice' ? block.choice_cards.length
                    : 1;
                if (remaining < count) {
                    session.currentBlock.value = b;
                    session.currentItem.value = remaining;
                    screen.value = 'journey';
                    return;
                }
                remaining -= count;
            }
            screen.value = 'end';
        }
    }
});

async function start() {
    await session.startSession();
    screen.value = 'journey';
    posthog.capture('journey_started', {
        journey_slug: props.journey.slug,
        journey_title: props.journey.title,
    });
}

function restart() {
    localStorage.removeItem(`du-journey-session-${props.journey.slug}`);
    screen.value = 'start';
    posthog.capture('journey_restarted', {
        journey_slug: props.journey.slug,
        journey_title: props.journey.title,
    });
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
    if (
        session.answeredQuizCount.value === 1 &&
        !session.personaPromptShown.value
    ) {
        screen.value = 'persona';
        return;
    }
    advanceToNext();
}

async function onPersonaSet(style: string) {
    await session.setPersona(style);
    posthog.capture('persona_selected', {
        persona_style: style,
        journey_slug: props.journey.slug,
    });

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
        } catch {
            /* retry */
        }
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
    if (session.totalQuizCards.value === 0)
        return 'Du hast die Lernreise abgeschlossen.';
    const pct = session.correctCount.value / session.totalQuizCards.value;
    if (pct >= 0.9)
        return 'Beeindruckend. Du erkennst die Realität besser als die meisten KI-Modelle.';
    if (pct >= 0.7)
        return 'Du hast ein solides Gespür dafür, was in der KI-Welt möglich ist — und was nicht.';
    if (pct >= 0.5)
        return 'Die Grenze zwischen Realität und Fiktion verschwimmt. Genau das macht KI so faszinierend — und gefährlich.';
    return 'Die Realität ist absurder als jede Fiktion. Genau der Punkt: Wir müssen lernen, KI-Geschichten kritisch einzuordnen.';
});
</script>

<template>
    <div :class="['flex flex-col bg-[#f5f5f7] font-sans dark:bg-black', screen === 'journey' ? 'h-dvh' : 'min-h-dvh']">
        <!-- LOADING -->
        <div
            v-if="screen === 'loading'"
            class="flex flex-1 items-center justify-center"
        >
            <div
                class="h-6 w-6 animate-spin rounded-full border-2 border-[#007aff] border-t-transparent"
            />
        </div>

        <!-- START -->
        <div
            v-else-if="screen === 'start'"
            class="flex flex-1 items-center justify-center overflow-y-auto px-5 py-8"
        >
            <div
                class="w-full max-w-3xl rounded-[32px] bg-white p-6 shadow-[0_30px_80px_-40px_rgba(0,0,0,0.3)] ring-1 ring-black/[0.05] sm:p-8 dark:bg-[#101114] dark:ring-white/[0.08]"
            >
                <div
                    class="grid gap-6 md:grid-cols-[1.05fr_0.95fr] md:items-start"
                >
                    <div>
                        <div
                            class="mb-5 flex h-18 w-18 items-center justify-center rounded-[22px] bg-gradient-to-b from-[#007aff] to-[#0055d4] shadow-lg shadow-[#007aff]/20"
                        >
                            <Zap class="h-9 w-9 text-white" />
                        </div>
                        <p
                            class="text-[12px] font-semibold tracking-[0.14em] text-[#007aff] uppercase"
                        >
                            Challenge + Gewinnspiel
                        </p>
                        <h1
                            class="mt-3 text-[34px] leading-[1.02] font-bold tracking-[-1px] text-[#1d1d1f] dark:text-[#f5f5f7]"
                        >
                            {{ journey.title }}
                        </h1>
                        <p
                            v-if="journey.description"
                            class="mt-4 max-w-[34rem] text-[17px] leading-[1.6] text-[#5f5f66] dark:text-[#98989d]"
                        >
                            {{ journey.description }}
                        </p>

                        <div class="mt-6 flex flex-wrap gap-3">
                            <div
                                class="rounded-full bg-[#f2f7ff] px-4 py-2 text-[14px] font-medium text-[#35506b] dark:bg-white/6 dark:text-white/70"
                            >
                                {{ session.displayStepTotal.value }} Fragen
                            </div>
                            <div
                                class="rounded-full bg-[#f6f6f7] px-4 py-2 text-[14px] font-medium text-[#5f5f66] dark:bg-white/6 dark:text-white/55"
                            >
                                ca. 5 bis 10 Minuten
                            </div>
                        </div>

                        <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                            <button
                                class="flex cursor-pointer items-center justify-center gap-2.5 rounded-full bg-[#007aff] px-8 py-4 text-[17px] font-semibold tracking-[-0.2px] text-white shadow-lg shadow-[#007aff]/25 transition-all active:scale-[0.97]"
                                @click="start"
                            >
                                Challenge starten
                                <ArrowRight class="h-4.5 w-4.5" />
                            </button>
                            <button
                                type="button"
                                class="flex items-center justify-center gap-2.5 rounded-full border border-black/10 px-8 py-4 text-[17px] font-medium text-[#5f5f66] transition hover:bg-black/[0.03] dark:border-white/10 dark:text-[#c5c5cb] dark:hover:bg-white/[0.04]"
                                @click="isGiveawayModalOpen = true"
                            >
                                <CircleHelp class="h-4.5 w-4.5" />
                                Mehr erfahren
                            </button>
                        </div>

                        <Link
                            href="/"
                            class="mt-5 inline-flex text-[15px] font-medium text-[#86868b] transition-colors hover:text-[#1d1d1f] dark:text-[#98989d] dark:hover:text-[#f5f5f7]"
                        >
                            Zur Übersicht
                        </Link>
                    </div>

                    <div
                        class="rounded-[28px] bg-[#f5f9ff] p-5 ring-1 ring-[#007aff]/10 sm:p-6 dark:bg-[#14161b] dark:ring-white/6"
                    >
                        <div class="flex items-start gap-3">
                            <div
                                class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-[#007aff] text-white"
                            >
                                <Gift class="h-6 w-6" />
                            </div>
                            <div>
                                <p
                                    class="text-[12px] font-semibold tracking-[0.14em] text-[#007aff] uppercase dark:text-[#66b3ff]"
                                >
                                    Zu gewinnen
                                </p>
                                <h2
                                    class="mt-2 text-[24px] leading-[1.12] font-bold tracking-[-0.5px] text-[#1d1d1f] dark:text-[#f5f5f7]"
                                >
                                    AI Training + Workshop
                                </h2>
                            </div>
                        </div>
                        <p
                            class="mt-4 text-[16px] leading-[1.65] text-[#4b5563] dark:text-white/60"
                        >
                            Für dich und deine Firma. Wert: 1.990 € netto. Du
                            spielst die Challenge durch und trägst am Ende deine
                            E-Mail-Adresse ein.
                        </p>
                        <div
                            class="mt-5 rounded-2xl bg-white p-4 ring-1 ring-black/5 dark:bg-white/5 dark:ring-white/8"
                        >
                            <p
                                class="text-[13px] font-semibold tracking-[0.12em] text-[#5f5f66] uppercase dark:text-white/35"
                            >
                                Wichtig
                            </p>
                            <p
                                class="mt-2 text-[14px] leading-[1.6] text-[#5f5f66] dark:text-white/55"
                            >
                                Wenn du das Training direkt kaufst und
                                gleichzeitig gewinnst, wird der Kauf
                                zurückerstattet.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- JOURNEY -->
        <template v-else-if="screen === 'journey' && currentBlock">
            <header
                class="flex shrink-0 items-center justify-between px-5 pt-4 pb-3"
            >
                <span
                    class="text-[17px] font-semibold tracking-[-0.2px] text-[#1d1d1f] dark:text-[#f5f5f7]"
                >
                    {{ journey.title }}
                </span>
                <span
                    v-if="currentBlock?.type === 'quiz'"
                    class="text-[15px] font-medium text-[#86868b] dark:text-[#98989d]"
                >
                    {{ session.displayStepCurrent.value + 1 }} /
                    {{ session.displayStepTotal.value }}
                </span>
            </header>

            <div
                class="mx-5 mb-4 h-[3px] overflow-hidden rounded-full bg-black/[0.06] dark:bg-white/[0.08]"
            >
                <div
                    class="h-full rounded-full bg-[#007aff] transition-all duration-500 ease-out"
                    :style="{ width: session.progress.value + '%' }"
                />
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
                    :web-search="currentBlock.config?.web_search ?? false"
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
                    :config="getTimelineConfig(currentBlock.config)"
                    @next="advanceToNext"
                />
            </Transition>
        </template>

        <!-- PERSONA -->
        <template v-else-if="screen === 'persona'">
            <header
                class="flex shrink-0 items-center justify-between px-5 pt-4 pb-3"
            >
                <span
                    class="text-[17px] font-semibold tracking-[-0.2px] text-[#1d1d1f] dark:text-[#f5f5f7]"
                >
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
            <header
                class="flex shrink-0 items-center justify-between px-5 pt-4 pb-3"
            >
                <span
                    class="text-[17px] font-semibold tracking-[-0.2px] text-[#1d1d1f] dark:text-[#f5f5f7]"
                >
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
        <div
            v-else-if="screen === 'end'"
            class="flex min-h-0 flex-1 flex-col overflow-y-auto px-5 pt-8 pb-8"
        >
            <!-- Score -->
            <div class="mb-6 flex flex-col items-center text-center">
                <div
                    v-if="session.totalQuizCards.value > 0"
                    class="mb-5 flex h-32 w-32 flex-col items-center justify-center rounded-full border-[5px] border-[#007aff]"
                >
                    <span
                        class="text-[36px] leading-none font-extrabold text-[#007aff]"
                    >
                        {{ session.correctCount.value }}/{{
                            session.totalQuizCards.value
                        }}
                    </span>
                    <span
                        class="mt-0.5 text-[12px] text-[#86868b] dark:text-[#98989d]"
                        >richtig</span
                    >
                </div>

                <h1
                    class="mb-2 text-[28px] leading-[1.15] font-extrabold tracking-[-0.6px] text-[#1d1d1f] dark:text-[#f5f5f7]"
                >
                    {{ endTitle }}
                </h1>
                <p
                    class="mb-6 max-w-[300px] text-[15px] leading-[1.5] text-[#86868b] dark:text-[#98989d]"
                >
                    {{ endMessage }}
                </p>
            </div>

            <div
                class="mb-4 overflow-hidden rounded-2xl bg-white ring-1 ring-black/[0.04] dark:bg-[#1c1c1e] dark:ring-white/[0.06]"
            >
                <div class="p-6">
                    <p
                        class="mb-1 text-[11px] font-bold tracking-wider text-[#34c759] uppercase"
                    >
                        Verlosung
                    </p>
                    <h3
                        class="mb-2 text-[22px] leading-[1.2] font-bold tracking-[-0.4px] text-[#1d1d1f] dark:text-[#f5f5f7]"
                    >
                        Jetzt E-Mail eintragen und Platz im Lostopf sichern
                    </h3>
                    <p
                        class="mb-5 text-[15px] leading-[1.5] text-[#86868b] dark:text-[#98989d]"
                    >
                        Du hast die Challenge abgeschlossen. Trag jetzt deine
                        E-Mail-Adresse ein. LinkedIn ist optional. Double
                        Opt-ins schicken wir separat raus.
                    </p>
                    <NewsletterLeadForm
                        source="challenge_complete"
                        :journey-slug="journey.slug"
                        :journey-session-nanoid="session.sessionId.value"
                        :completed-challenge="true"
                        submit-label="Für Verlosung eintragen"
                        success-title="Du bist im Lostopf"
                        success-message="Dein Eintrag ist gespeichert. Wenn du das Training heute direkt buchst und gewinnst, erstatten wir den Kauf vollständig zurück."
                    />
                </div>
            </div>

            <!-- AI Ready CTA Card -->
            <div
                class="mb-4 overflow-hidden rounded-2xl bg-white ring-1 ring-black/[0.04] dark:bg-[#1c1c1e] dark:ring-white/[0.06]"
            >
                <div class="p-6">
                    <p
                        class="mb-1 text-[11px] font-bold tracking-wider text-[#007aff] uppercase"
                    >
                        Hands-on KI-Training
                    </p>
                    <h3
                        class="mb-2 text-[22px] leading-[1.2] font-bold tracking-[-0.4px] text-[#1d1d1f] dark:text-[#f5f5f7]"
                    >
                        Dein Team soll eigene KI-Agenten bauen?
                    </h3>
                    <p
                        class="mb-5 text-[15px] leading-[1.5] text-[#86868b] dark:text-[#98989d]"
                    >
                        In unserem AI Ready Training lernt dein Team hands-on,
                        KI-Agenten zu bauen, Chancen und Grenzen einzuschätzen
                        und KI strategisch einzusetzen — mit genau solchen
                        interaktiven Formaten wie diesem Quiz.
                    </p>
                    <Link
                        :href="aiReadyIndex()"
                        class="flex w-full items-center justify-center gap-2 rounded-xl bg-[#1d1d1f] px-5 py-3.5 text-[17px] font-semibold tracking-[-0.2px] text-white transition-all active:scale-[0.97] dark:bg-[#f5f5f7] dark:text-[#1d1d1f]"
                        @click="
                            posthog.capture('ai_ready_cta_clicked', {
                                source: 'journey_end',
                                journey_slug: journey.slug,
                            })
                        "
                    >
                        Direkt zum Angebot
                        <Sparkles class="h-4.5 w-4.5" />
                    </Link>
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

        <TrainingGiveawayModal
            :open="isGiveawayModalOpen"
            @close="isGiveawayModalOpen = false"
        />
    </div>
</template>
