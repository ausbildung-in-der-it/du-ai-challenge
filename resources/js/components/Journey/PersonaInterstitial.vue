<script setup lang="ts">
import { ref } from 'vue';
import { Sparkles, ChevronRight, Code, Wand2 } from 'lucide-vue-next';

const emit = defineEmits<{
    setPersona: [style: string];
    skip: [];
}>();

const style = ref('');
const submitted = ref(false);

function submit() {
    if (!style.value.trim()) return;
    submitted.value = true;
    emit('setPersona', style.value.trim());
}
</script>

<template>
    <div class="flex min-h-0 flex-1 flex-col">
        <div class="flex-1 overflow-y-auto px-5 pb-4">
            <div
                class="rounded-2xl bg-white shadow-sm ring-1 ring-black/[0.04] dark:bg-[#1c1c1e] dark:ring-white/[0.06]"
            >
                <div class="p-6">
                    <!-- Icon -->
                    <div
                        class="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-purple-500/10"
                    >
                        <Wand2 class="h-6 w-6 text-purple-500" />
                    </div>

                    <h2
                        class="mb-2 text-[22px] leading-[1.25] font-bold tracking-[-0.4px] text-[#1d1d1f] dark:text-[#f5f5f7]"
                    >
                        Das war ein KI-Kommentar
                    </h2>

                    <p class="mb-5 text-[15px] leading-[1.6] text-[#86868b] dark:text-[#98989d]">
                        Die KI hat gerade deine Antwort kommentiert. Aber du
                        kannst bestimmen, <strong class="text-[#1d1d1f] dark:text-[#f5f5f7]">wie</strong> sie spricht.
                    </p>

                    <p class="mb-4 text-[14px] leading-[1.5] text-[#86868b] dark:text-[#98989d]">
                        Gib einen Stil ein — z.B. "rede wie ein Pirat", "auf Englisch",
                        "wie ein Sportkommentator" oder "im Stil von Yoda".
                    </p>

                    <!-- Input -->
                    <div class="relative mb-3">
                        <input
                            v-model="style"
                            type="text"
                            placeholder="z.B. rede wie ein Pirat"
                            maxlength="200"
                            class="w-full rounded-xl border border-black/[0.08] bg-[#f5f5f7] px-4 py-3.5 text-[16px] text-[#1d1d1f] placeholder-[#86868b] outline-none transition-all focus:border-[#007aff] focus:ring-2 focus:ring-[#007aff]/20 dark:border-white/[0.08] dark:bg-[#2c2c2e] dark:text-[#f5f5f7] dark:placeholder-[#98989d]"
                            @keydown.enter="submit"
                        />
                    </div>

                    <p class="text-[12px] text-[#86868b]/60 dark:text-[#98989d]/60">
                        Vorsicht: Das lässt sich danach nicht mehr ändern.
                    </p>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="shrink-0 px-5 pb-5">
            <button
                class="mb-2 flex w-full cursor-pointer items-center justify-center gap-2 rounded-2xl bg-purple-500 px-4 py-4 text-[17px] font-semibold tracking-[-0.2px] text-white transition-all active:scale-[0.97] disabled:opacity-40"
                :disabled="!style.trim()"
                @click="submit"
            >
                <Sparkles class="h-5 w-5" />
                Ausprobieren
            </button>
            <button
                class="flex w-full cursor-pointer items-center justify-center gap-2 rounded-2xl px-4 py-3 text-[15px] font-medium text-[#86868b] transition-all active:scale-[0.97] dark:text-[#98989d]"
                @click="$emit('skip')"
            >
                Überspringen
            </button>
        </div>
    </div>
</template>
