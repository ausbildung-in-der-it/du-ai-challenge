<script setup lang="ts">
import { ref, computed } from 'vue';
import { Check, X, ChevronRight, HelpCircle } from 'lucide-vue-next';

const props = defineProps<{
    card: App.Data.ChoiceCardData;
}>();

defineEmits<{
    answered: [correct: boolean];
    next: [];
}>();

const selectedIndex = ref<number | null>(null);
const revealed = ref(false);

const isCorrect = computed(() => selectedIndex.value === props.card.correct_index);

function select(index: number) {
    if (revealed.value) return;
    selectedIndex.value = index;
    revealed.value = true;
}

function optionClass(index: number): string {
    if (!revealed.value) {
        return 'bg-[#f5f5f7] dark:bg-[#2c2c2e] active:scale-[0.98]';
    }
    if (index === props.card.correct_index) {
        return 'bg-[#34c759]/10 ring-2 ring-[#34c759]';
    }
    if (index === selectedIndex.value && index !== props.card.correct_index) {
        return 'bg-[#ff3b30]/10 ring-2 ring-[#ff3b30]';
    }
    return 'bg-[#f5f5f7] opacity-40 dark:bg-[#2c2c2e]';
}
</script>

<template>
    <div class="flex min-h-0 flex-1 flex-col">
        <div class="flex-1 overflow-y-auto px-5 pb-4">
            <div class="rounded-2xl bg-white shadow-sm ring-1 ring-black/[0.04] dark:bg-[#1c1c1e] dark:ring-white/[0.06]">
                <div class="p-6">
                    <div class="mb-4 inline-flex h-10 w-10 items-center justify-center rounded-xl bg-[#5856d6]/10">
                        <HelpCircle class="h-5 w-5 text-[#5856d6]" />
                    </div>

                    <h2 class="mb-5 text-[20px] leading-[1.3] font-bold tracking-[-0.3px] text-[#1d1d1f] dark:text-[#f5f5f7]">
                        {{ card.question }}
                    </h2>

                    <!-- Options -->
                    <div class="space-y-2.5">
                        <button
                            v-for="(option, i) in card.options"
                            :key="i"
                            class="flex w-full cursor-pointer items-start gap-3 rounded-xl p-3.5 text-left transition-all"
                            :class="optionClass(i)"
                            :disabled="revealed"
                            @click="select(i)"
                        >
                            <!-- Letter circle -->
                            <span
                                class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full text-[13px] font-bold"
                                :class="revealed && i === card.correct_index
                                    ? 'bg-[#34c759] text-white'
                                    : revealed && i === selectedIndex && i !== card.correct_index
                                        ? 'bg-[#ff3b30] text-white'
                                        : 'bg-black/[0.06] text-[#86868b] dark:bg-white/[0.08] dark:text-[#98989d]'"
                            >
                                <Check v-if="revealed && i === card.correct_index" class="h-4 w-4" />
                                <X v-else-if="revealed && i === selectedIndex && i !== card.correct_index" class="h-4 w-4" />
                                <span v-else>{{ String.fromCharCode(65 + i) }}</span>
                            </span>

                            <span
                                class="pt-0.5 text-[15px] leading-[1.5]"
                                :class="revealed && i === card.correct_index
                                    ? 'font-semibold text-[#34c759]'
                                    : 'text-[#1d1d1f] dark:text-[#f5f5f7]'"
                            >
                                {{ option }}
                            </span>
                        </button>
                    </div>

                    <!-- Explanation -->
                    <Transition
                        enter-active-class="transition-all duration-400 ease-out"
                        enter-from-class="opacity-0 translate-y-3"
                        enter-to-class="opacity-100 translate-y-0"
                    >
                        <div v-if="revealed" class="mt-5 border-t border-black/[0.06] pt-4 dark:border-white/[0.08]">
                            <p
                                class="mb-3 text-[15px] font-semibold"
                                :class="isCorrect ? 'text-[#34c759]' : 'text-[#ff3b30]'"
                            >
                                {{ isCorrect ? 'Richtig!' : 'Nicht ganz.' }}
                            </p>
                            <p class="text-[15px] leading-[1.6] text-[#1d1d1f] dark:text-[#f5f5f7]">
                                {{ card.explanation }}
                            </p>
                        </div>
                    </Transition>
                </div>
            </div>
        </div>

        <div class="shrink-0 px-5 pb-5">
            <button
                v-if="revealed"
                class="flex w-full cursor-pointer items-center justify-center gap-2 rounded-2xl bg-[#007aff] px-4 py-4 text-[17px] font-semibold tracking-[-0.2px] text-white transition-all active:scale-[0.97]"
                @click="$emit('next')"
            >
                Weiter
                <ChevronRight class="h-5 w-5" />
            </button>
        </div>
    </div>
</template>
