<script setup lang="ts">
import { ChevronRight, Lightbulb } from 'lucide-vue-next';

defineProps<{
    card: App.Data.LearnCardData;
}>();

defineEmits<{
    next: [];
}>();
</script>

<template>
    <div class="flex min-h-0 flex-1 flex-col">
        <!-- Card -->
        <div class="flex-1 overflow-y-auto px-5 pb-4">
            <div
                class="rounded-2xl bg-white shadow-sm ring-1 ring-black/[0.04] dark:bg-[#1c1c1e] dark:ring-white/[0.06]"
            >
                <div class="p-6">
                    <!-- Icon -->
                    <div
                        class="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-[#007aff]/10"
                    >
                        <Lightbulb class="h-6 w-6 text-[#007aff]" />
                    </div>

                    <!-- Title -->
                    <h2
                        class="mb-4 text-[22px] leading-[1.25] font-bold tracking-[-0.4px] text-[#1d1d1f] dark:text-[#f5f5f7]"
                    >
                        {{ card.title }}
                    </h2>

                    <!-- Content -->
                    <div
                        class="space-y-4 text-[16px] leading-[1.6] text-[#86868b] dark:text-[#98989d]"
                    >
                        <p
                            v-for="(paragraph, i) in card.content.split('\n\n')"
                            :key="i"
                        >
                            {{ paragraph }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action -->
        <div class="shrink-0 px-5 pb-5">
            <button
                class="flex w-full cursor-pointer items-center justify-center gap-2 rounded-2xl bg-[#007aff] px-4 py-4 text-[17px] font-semibold tracking-[-0.2px] text-white transition-all active:scale-[0.97]"
                @click="$emit('next')"
            >
                Verstanden
                <ChevronRight class="h-5 w-5" />
            </button>
        </div>
    </div>
</template>
