<script setup lang="ts">
import { computed, watch } from 'vue';
import { Code, ChevronRight, Sparkles } from 'lucide-vue-next';
import { useTypewriter } from '@/composables/useTypewriter';

const props = defineProps<{
    promptUsed: string | null;
    commentaryText: string | null;
    isLoading: boolean;
}>();

defineEmits<{
    next: [];
}>();

const promptTypewriter = useTypewriter(8);
const commentaryTypewriter = useTypewriter(20);

// Typewrite the prompt when it arrives
watch(
    () => props.promptUsed,
    (text) => {
        if (text) promptTypewriter.start(text);
    },
);

// Typewrite the commentary when it arrives
watch(
    () => props.commentaryText,
    (text) => {
        if (text) commentaryTypewriter.start(text);
    },
);

// Highlight "WICHTIG" in the displayed prompt
const highlightedPrompt = computed(() => {
    const text = promptTypewriter.displayed.value;
    if (!text) return '';
    return text.replace(
        /WICHTIG/g,
        '<span class="font-bold text-[#ff9f0a]">WICHTIG</span>',
    );
});

const allDone = computed(
    () =>
        !props.isLoading &&
        !promptTypewriter.isTyping.value &&
        !commentaryTypewriter.isTyping.value &&
        !!props.commentaryText,
);
</script>

<template>
    <div class="flex min-h-0 flex-1 flex-col">
        <div class="flex-1 overflow-y-auto px-5 pb-4">
            <div
                class="rounded-2xl bg-white shadow-sm ring-1 ring-black/[0.04] dark:bg-[#1c1c1e] dark:ring-white/[0.06]"
            >
                <div class="p-6">
                    <div
                        class="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-[#007aff]/10"
                    >
                        <Code class="h-6 w-6 text-[#007aff]" />
                    </div>

                    <h2
                        class="mb-2 text-[22px] leading-[1.25] font-bold tracking-[-0.4px] text-[#1d1d1f] dark:text-[#f5f5f7]"
                    >
                        Du hast gerade gepromptet!
                    </h2>

                    <p
                        class="mb-4 text-[15px] leading-[1.6] text-[#86868b] dark:text-[#98989d]"
                    >
                        Mit deiner Stil-Anweisung hast du das Verhalten eines
                        KI-Modells gesteuert. Das ist
                        <strong class="text-[#1d1d1f] dark:text-[#f5f5f7]"
                            >Prompt Engineering</strong
                        >
                        — du sagst der KI nicht nur <em>was</em>, sondern
                        <em>wie</em> sie antworten soll.
                    </p>

                    <!-- Prompt Display (typewriter + highlighting) -->
                    <div
                        v-if="promptTypewriter.displayed.value || promptUsed"
                        class="mb-4 overflow-hidden rounded-xl bg-[#1d1d1f] p-4 dark:bg-black"
                    >
                        <p
                            class="mb-1 text-[11px] font-bold tracking-wider text-[#86868b] uppercase"
                        >
                            System Prompt
                        </p>
                        <!-- eslint-disable-next-line vue/no-v-html -->
                        <p
                            class="whitespace-pre-wrap font-mono text-[12px] leading-[1.6] text-[#98989d]"
                            v-html="highlightedPrompt"
                        />
                        <span
                            v-if="promptTypewriter.isTyping.value"
                            class="ml-0.5 inline-block h-[13px] w-[2px] animate-pulse bg-[#007aff]"
                        />
                    </div>

                    <!-- AI Commentary with persona (typewriter) -->
                    <div
                        v-if="commentaryTypewriter.displayed.value || isLoading"
                        class="flex items-start gap-2"
                    >
                        <Sparkles
                            class="mt-0.5 h-4 w-4 shrink-0 text-purple-500 opacity-60"
                        />
                        <p
                            class="text-[15px] leading-[1.5] text-[#1d1d1f] italic dark:text-[#f5f5f7]"
                        >
                            {{ commentaryTypewriter.displayed.value
                            }}<span
                                v-if="
                                    isLoading ||
                                    commentaryTypewriter.isTyping.value
                                "
                                class="ml-0.5 inline-block h-[14px] w-[2px] animate-pulse bg-purple-500"
                            />
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action -->
        <div class="shrink-0 px-5 pb-5">
            <button
                class="flex w-full cursor-pointer items-center justify-center gap-2 rounded-2xl bg-[#007aff] px-4 py-4 text-[17px] font-semibold tracking-[-0.2px] text-white transition-all active:scale-[0.97] disabled:opacity-40"
                :disabled="!allDone"
                @click="$emit('next')"
            >
                Weiter
                <ChevronRight class="h-5 w-5" />
            </button>
        </div>
    </div>
</template>
