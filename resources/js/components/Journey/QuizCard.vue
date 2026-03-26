<script setup lang="ts">
import { ref, computed, nextTick } from 'vue';
import { X, Check, ExternalLink, ChevronRight, Calendar, Sparkles } from 'lucide-vue-next';
import { useStreamText } from '@/composables/useStreamText';

const props = defineProps<{
    card: App.Data.QuizCardData;
    sessionId: string | null;
    showWeiterButton: boolean;
}>();

const emit = defineEmits<{
    answered: [userSaidReal: boolean];
    next: [];
    commentaryDone: [text: string];
}>();

const revealed = ref(false);
const userAnswer = ref<boolean | null>(null);
const revealEl = ref<HTMLElement | null>(null);
const commentary = useStreamText();
const commentaryRequested = ref(false);

const isCorrect = computed(() => userAnswer.value === props.card.is_real);

const realPhrases = [
    'Das ist tatsächlich so passiert.',
    'Kein Witz — das ist real.',
    'So geschehen. Wirklich.',
    'Willkommen in der Realität.',
    'Klingt verrückt, ist aber wahr.',
    'Die Realität übertrifft jede Fiktion.',
];

const fakePhrases = [
    'Das ist zum Glück nicht passiert.',
    'Nein — das haben wir erfunden.',
    'Frei erfunden. Dieses Mal.',
    'Noch ist das Fiktion.',
    'Das ist nicht wahr. Noch nicht.',
    'Pure Erfindung. Vorerst.',
];

const revealHeadline = ref('');

async function answer(saidReal: boolean) {
    if (revealed.value) return;
    userAnswer.value = saidReal;
    revealed.value = true;

    const phrases = props.card.is_real ? realPhrases : fakePhrases;
    revealHeadline.value = phrases[Math.floor(Math.random() * phrases.length)];

    emit('answered', saidReal);

    nextTick(() => {
        revealEl.value?.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    });

    // Stream commentary with dynamic URL
    if (props.sessionId) {
        commentaryRequested.value = true;
        const result = await commentary.stream(
            `/api/sessions/${props.sessionId}/commentaries`,
            { quiz_card_id: props.card.id },
        );
        emit('commentaryDone', result);
    }
}
</script>

<template>
    <div class="flex min-h-0 flex-1 flex-col">
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
                            <p
                                :class="[
                                    'mb-3 text-[18px] leading-[1.3] font-bold tracking-[-0.3px]',
                                    card.is_real ? 'text-[#34c759]' : 'text-[#ff3b30]',
                                ]"
                            >
                                {{ revealHeadline }}
                            </p>

                            <!-- AI Commentary (SSE streamed) -->
                            <div v-if="commentaryRequested" class="mb-4 flex items-start gap-2">
                                <Sparkles class="mt-0.5 h-4 w-4 shrink-0 text-[#007aff] opacity-60" />
                                <p class="text-[14px] leading-[1.5] text-[#86868b] italic dark:text-[#98989d]">
                                    {{ commentary.text.value }}<span
                                        v-if="commentary.isLoading.value || !commentary.text.value"
                                        class="ml-0.5 inline-block h-[14px] w-[2px] animate-pulse bg-[#007aff]"
                                    />
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
            <div v-else-if="showWeiterButton && !commentary.isLoading.value">
                <button class="flex w-full cursor-pointer items-center justify-center gap-2 rounded-2xl bg-[#007aff] px-4 py-4 text-[17px] font-semibold tracking-[-0.2px] text-white transition-all active:scale-[0.97]" @click="$emit('next')">
                    Weiter <ChevronRight class="h-5 w-5" />
                </button>
            </div>
        </div>
    </div>
</template>
