<script setup lang="ts">
import { ref, computed, nextTick } from 'vue';
import { X, Check, ExternalLink, ChevronRight, Calendar, Sparkles } from 'lucide-vue-next';

const props = defineProps<{
    card: App.Data.QuizCardData;
    commentaryText: string | null;
    commentaryLoading: boolean;
    showWeiterButton: boolean;
}>();

const emit = defineEmits<{
    answered: [userSaidReal: boolean];
    next: [];
}>();

const revealed = ref(false);
const userAnswer = ref<boolean | null>(null);
const revealEl = ref<HTMLElement | null>(null);

const isCorrect = computed(() => userAnswer.value === props.card.is_real);

const hardcodedLine = computed(() => {
    if (userAnswer.value === null) return '';
    if (props.card.is_real) return 'Das ist tatsächlich so passiert.';
    return 'Das ist zum Glück nicht passiert.';
});

function answer(saidReal: boolean) {
    if (revealed.value) return;
    userAnswer.value = saidReal;
    revealed.value = true;
    emit('answered', saidReal);

    nextTick(() => {
        revealEl.value?.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    });
}
</script>

<template>
    <div class="flex min-h-0 flex-1 flex-col">
        <!-- Card -->
        <div class="flex-1 overflow-y-auto px-5 pb-4">
            <div class="rounded-2xl bg-white shadow-sm ring-1 ring-black/[0.04] dark:bg-[#1c1c1e] dark:ring-white/[0.06]">
                <div class="p-6">
                    <span class="mb-4 inline-block rounded-lg bg-[#007aff]/10 px-2.5 py-1 text-[11px] font-bold tracking-wider text-[#007aff] uppercase">
                        {{ card.category }}
                    </span>

                    <h2 class="mb-4 text-[22px] leading-[1.25] font-bold tracking-[-0.4px] text-[#1d1d1f] dark:text-[#f5f5f7]">
                        {{ card.headline }}
                    </h2>

                    <p class="mb-5 text-[16px] leading-[1.6] text-[#86868b] dark:text-[#98989d]">
                        {{ card.story }}
                    </p>

                    <div class="flex items-center gap-1.5 text-[13px] text-[#86868b] dark:text-[#98989d]">
                        <Calendar class="h-3.5 w-3.5 opacity-50" />
                        {{ card.date_label }}
                    </div>

                    <!-- Reveal -->
                    <Transition
                        enter-active-class="transition-all duration-400 ease-out"
                        enter-from-class="opacity-0 translate-y-3"
                        enter-to-class="opacity-100 translate-y-0"
                    >
                        <div v-if="revealed" ref="revealEl" class="mt-5 border-t border-black/[0.06] pt-5 dark:border-white/[0.08]">
                            <span :class="['mb-3 inline-flex items-center gap-1 rounded-lg px-2.5 py-1 text-[13px] font-semibold', isCorrect ? 'bg-[#34c759]/10 text-[#34c759]' : 'bg-[#ff3b30]/10 text-[#ff3b30]']">
                                <Check v-if="isCorrect" class="h-3.5 w-3.5" />
                                <X v-else class="h-3.5 w-3.5" />
                                {{ isCorrect ? 'Richtig!' : 'Falsch!' }}
                            </span>

                            <div :class="['mb-3 inline-flex items-center gap-1.5 rounded-full px-4 py-2 text-[15px] font-bold', card.is_real ? 'bg-[#34c759]/10 text-[#34c759]' : 'bg-[#ff3b30]/10 text-[#ff3b30]']">
                                <Check v-if="card.is_real" class="h-[18px] w-[18px]" />
                                <X v-else class="h-[18px] w-[18px]" />
                                {{ card.is_real ? 'Echt passiert' : 'Erfunden' }}
                            </div>

                            <p class="mb-2 text-[15px] font-medium text-[#1d1d1f] dark:text-[#f5f5f7]">
                                {{ hardcodedLine }}
                            </p>

                            <!-- AI Commentary (polled) -->
                            <div v-if="commentaryText || commentaryLoading" class="mb-4 flex items-start gap-2">
                                <Sparkles class="mt-0.5 h-4 w-4 shrink-0 text-[#007aff] opacity-60" />
                                <p class="text-[14px] leading-[1.5] text-[#86868b] italic dark:text-[#98989d]">
                                    {{ commentaryText }}<span v-if="commentaryLoading" class="ml-0.5 inline-block h-[14px] w-[2px] animate-pulse bg-[#007aff]" />
                                </p>
                            </div>

                            <p class="mb-4 text-[15px] leading-[1.6] text-[#1d1d1f] dark:text-[#f5f5f7]">
                                {{ card.explanation }}
                            </p>

                            <ul v-if="card.sources?.length" class="flex flex-col gap-1.5">
                                <li v-for="source in card.sources" :key="source.url">
                                    <a :href="source.url" target="_blank" rel="noopener" class="inline-flex items-center gap-1 text-[13px] text-[#007aff] hover:underline">
                                        <ExternalLink class="h-3 w-3 opacity-60" />
                                        {{ source.title }}
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </Transition>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="shrink-0 px-5 pb-5">
            <div v-if="!revealed" class="flex gap-3">
                <button class="flex flex-1 cursor-pointer items-center justify-center gap-2 rounded-2xl bg-[#ff3b30]/10 px-4 py-4 text-[17px] font-semibold tracking-[-0.2px] text-[#ff3b30] transition-all active:scale-[0.97]" @click="answer(false)">
                    <X class="h-5 w-5" /> Erfunden
                </button>
                <button class="flex flex-1 cursor-pointer items-center justify-center gap-2 rounded-2xl bg-[#34c759]/10 px-4 py-4 text-[17px] font-semibold tracking-[-0.2px] text-[#34c759] transition-all active:scale-[0.97]" @click="answer(true)">
                    <Check class="h-5 w-5" /> Echt passiert
                </button>
            </div>
            <div v-else-if="showWeiterButton">
                <button class="flex w-full cursor-pointer items-center justify-center gap-2 rounded-2xl bg-[#007aff] px-4 py-4 text-[17px] font-semibold tracking-[-0.2px] text-white transition-all active:scale-[0.97]" @click="$emit('next')">
                    Weiter <ChevronRight class="h-5 w-5" />
                </button>
            </div>
        </div>
    </div>
</template>
