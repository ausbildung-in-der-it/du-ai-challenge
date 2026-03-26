<script setup lang="ts">
import { ref, computed } from 'vue';
import { Play, RotateCcw } from 'lucide-vue-next';
import QuizCard from './QuizCard.vue';
import LearnCard from './LearnCard.vue';

const props = defineProps<{
    journey: App.Data.LearningJourneyData;
}>();

type Screen = 'start' | 'journey' | 'end';

const screen = ref<Screen>('start');
const blockIndex = ref(0);
const itemIndex = ref(0);
const correctCount = ref(0);
const totalQuizCards = ref(0);

const currentBlock = computed(() => props.journey.blocks[blockIndex.value]);

const totalSteps = computed(() =>
    props.journey.blocks.reduce((sum, block) => {
        if (block.type === 'quiz') return sum + block.quiz_cards.length;
        if (block.type === 'learn') return sum + block.learn_cards.length;
        return sum;
    }, 0),
);

const currentStep = computed(() => {
    let step = 0;
    for (let i = 0; i < blockIndex.value; i++) {
        const b = props.journey.blocks[i];
        if (b.type === 'quiz') step += b.quiz_cards.length;
        if (b.type === 'learn') step += b.learn_cards.length;
    }
    return step + itemIndex.value;
});

const progress = computed(() =>
    totalSteps.value === 0 ? 0 : (currentStep.value / totalSteps.value) * 100,
);

function start() {
    screen.value = 'journey';
    blockIndex.value = 0;
    itemIndex.value = 0;
    correctCount.value = 0;
    totalQuizCards.value = props.journey.blocks.reduce(
        (sum, b) => (b.type === 'quiz' ? sum + b.quiz_cards.length : sum),
        0,
    );
}

function onAnswered(correct: boolean) {
    if (correct) correctCount.value++;
}

function next() {
    const block = currentBlock.value;
    if (!block) return;

    const items =
        block.type === 'quiz' ? block.quiz_cards : block.learn_cards;

    if (itemIndex.value < items.length - 1) {
        itemIndex.value++;
    } else if (blockIndex.value < props.journey.blocks.length - 1) {
        blockIndex.value++;
        itemIndex.value = 0;
    } else {
        screen.value = 'end';
    }
}

const endTitle = computed(() => {
    if (totalQuizCards.value === 0) return 'Geschafft!';
    const pct = correctCount.value / totalQuizCards.value;
    if (pct >= 0.9) return 'KI-Experte!';
    if (pct >= 0.7) return 'Gut informiert!';
    if (pct >= 0.5) return 'Nicht schlecht!';
    return 'Willkommen in 2026.';
});

const endMessage = computed(() => {
    if (totalQuizCards.value === 0)
        return 'Du hast die Lernreise abgeschlossen.';
    const pct = correctCount.value / totalQuizCards.value;
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
    <div
        class="flex h-dvh flex-col bg-[#f5f5f7] font-sans dark:bg-black"
    >
        <!-- ===== START SCREEN ===== -->
        <div
            v-if="screen === 'start'"
            class="flex flex-1 flex-col items-center justify-center px-8 text-center"
        >
            <div class="mb-6 text-6xl">🤖</div>
            <h1
                class="mb-3 text-[32px] leading-[1.15] font-extrabold tracking-[-0.8px] text-[#1d1d1f] dark:text-[#f5f5f7]"
            >
                {{ journey.title }}
            </h1>
            <p
                v-if="journey.description"
                class="mb-10 max-w-[320px] text-[17px] leading-[1.5] text-[#86868b] dark:text-[#98989d]"
            >
                {{ journey.description }}
            </p>
            <button
                class="flex cursor-pointer items-center gap-2 rounded-2xl bg-[#007aff] px-12 py-4 text-[17px] font-semibold tracking-[-0.2px] text-white transition-all active:scale-[0.97]"
                @click="start"
            >
                <Play class="h-5 w-5" />
                Los geht's
            </button>
        </div>

        <!-- ===== JOURNEY SCREEN ===== -->
        <template v-else-if="screen === 'journey' && currentBlock">
            <!-- Header -->
            <header
                class="flex shrink-0 items-center justify-between px-5 pt-4 pb-3"
            >
                <span
                    class="text-[17px] font-semibold tracking-[-0.2px] text-[#1d1d1f] dark:text-[#f5f5f7]"
                >
                    {{ journey.title }}
                </span>
                <span
                    class="text-[15px] font-medium text-[#86868b] dark:text-[#98989d]"
                >
                    {{ currentStep + 1 }} / {{ totalSteps }}
                </span>
            </header>

            <!-- Progress -->
            <div
                class="mx-5 mb-4 h-[3px] overflow-hidden rounded-full bg-black/[0.06] dark:bg-white/[0.08]"
            >
                <div
                    class="h-full rounded-full bg-[#007aff] transition-all duration-500 ease-out"
                    :style="{ width: progress + '%' }"
                />
            </div>

            <!-- Block content -->
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
                    :key="`quiz-${blockIndex}-${itemIndex}`"
                    :card="currentBlock.quiz_cards[itemIndex]"
                    @answered="onAnswered"
                    @next="next"
                />
                <LearnCard
                    v-else-if="currentBlock.type === 'learn'"
                    :key="`learn-${blockIndex}-${itemIndex}`"
                    :card="currentBlock.learn_cards[itemIndex]"
                    @next="next"
                />
            </Transition>
        </template>

        <!-- ===== END SCREEN ===== -->
        <div
            v-else-if="screen === 'end'"
            class="flex flex-1 flex-col items-center justify-center px-8 text-center"
        >
            <div
                v-if="totalQuizCards > 0"
                class="mb-6 flex h-36 w-36 flex-col items-center justify-center rounded-full border-[6px] border-[#007aff]"
            >
                <span
                    class="text-[42px] leading-none font-extrabold text-[#007aff]"
                >
                    {{ correctCount }}/{{ totalQuizCards }}
                </span>
                <span
                    class="mt-0.5 text-[13px] text-[#86868b] dark:text-[#98989d]"
                >
                    richtig
                </span>
            </div>
            <div v-else class="mb-6 text-6xl">🎉</div>

            <h1
                class="mb-3 text-[32px] leading-[1.15] font-extrabold tracking-[-0.8px] text-[#1d1d1f] dark:text-[#f5f5f7]"
            >
                {{ endTitle }}
            </h1>
            <p
                class="mb-8 max-w-[300px] text-[17px] leading-[1.5] text-[#86868b] dark:text-[#98989d]"
            >
                {{ endMessage }}
            </p>
            <button
                class="flex cursor-pointer items-center gap-2 rounded-2xl bg-[#007aff] px-12 py-4 text-[17px] font-semibold tracking-[-0.2px] text-white transition-all active:scale-[0.97]"
                @click="screen = 'start'"
            >
                <RotateCcw class="h-5 w-5" />
                Nochmal spielen
            </button>
        </div>
    </div>
</template>
