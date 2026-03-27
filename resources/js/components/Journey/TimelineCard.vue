<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { ChevronRight, GitCommit, Clock, Sparkles, ArrowRight } from 'lucide-vue-next';

interface Commit {
    hash: string;
    message: string;
    time: string;
    minutes_in: number;
}

const props = defineProps<{
    config: {
        title: string;
        subtitle: string;
        commits: Commit[];
        total_commits: number;
        duration: string;
        teaser_headline: string;
        teaser_text: string;
    };
}>();

defineEmits<{ next: [] }>();

const visibleCount = ref(0);
const showTeaser = ref(false);

onMounted(() => {
    let i = 0;
    const interval = setInterval(() => {
        if (i < props.config.commits.length) {
            visibleCount.value = i + 1;
            i++;
        } else {
            clearInterval(interval);
            setTimeout(() => {
                showTeaser.value = true;
            }, 400);
        }
    }, 300);
});
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
                        class="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-[#30d158]/10"
                    >
                        <GitCommit class="h-6 w-6 text-[#30d158]" />
                    </div>

                    <!-- Title -->
                    <h2
                        class="mb-2 text-[22px] leading-[1.25] font-bold tracking-[-0.4px] text-[#1d1d1f] dark:text-[#f5f5f7]"
                    >
                        {{ config.title }}
                    </h2>

                    <p class="mb-5 text-[15px] leading-[1.6] text-[#86868b] dark:text-[#98989d]">
                        {{ config.subtitle }}
                    </p>

                    <!-- Duration badge -->
                    <div
                        class="mb-5 inline-flex items-center gap-1.5 rounded-full bg-[#30d158]/10 px-3 py-1.5"
                    >
                        <Clock class="h-3.5 w-3.5 text-[#30d158]" />
                        <span class="text-[13px] font-semibold text-[#30d158]">
                            {{ config.duration }}
                        </span>
                    </div>

                    <!-- Git Timeline -->
                    <div class="relative ml-3 border-l-2 border-[#30d158]/20 pl-5">
                        <TransitionGroup
                            enter-active-class="transition-all duration-400 ease-out"
                            enter-from-class="opacity-0 translate-x-3"
                            enter-to-class="opacity-100 translate-x-0"
                        >
                            <div
                                v-for="(commit, i) in config.commits.slice(0, visibleCount)"
                                :key="commit.hash"
                                class="relative mb-4 last:mb-0"
                            >
                                <!-- Dot on timeline -->
                                <div
                                    class="absolute -left-[27px] top-[5px] h-3 w-3 rounded-full border-2 border-[#30d158] bg-white dark:bg-[#1c1c1e]"
                                />

                                <!-- Time label -->
                                <span
                                    class="text-[11px] font-medium tracking-wide text-[#30d158] tabular-nums"
                                >
                                    {{ commit.time }} Uhr
                                    <span class="text-[#86868b]/50 dark:text-[#98989d]/50">
                                        · +{{ commit.minutes_in }} min
                                    </span>
                                </span>

                                <!-- Commit message -->
                                <p
                                    class="mt-0.5 text-[14px] leading-[1.4] text-[#1d1d1f] dark:text-[#f5f5f7]"
                                >
                                    {{ commit.message }}
                                </p>

                                <!-- Hash -->
                                <code
                                    class="mt-0.5 text-[11px] text-[#86868b]/40 dark:text-[#98989d]/40"
                                >
                                    {{ commit.hash }}
                                </code>
                            </div>
                        </TransitionGroup>

                        <!-- More commits teaser -->
                        <Transition
                            enter-active-class="transition-all duration-400 ease-out"
                            enter-from-class="opacity-0"
                            enter-to-class="opacity-100"
                        >
                            <div
                                v-if="visibleCount >= config.commits.length"
                                class="relative mb-0 pt-1"
                            >
                                <div
                                    class="absolute -left-[27px] top-[9px] h-3 w-3 rounded-full border-2 border-dashed border-[#86868b]/30 bg-white dark:bg-[#1c1c1e]"
                                />
                                <p
                                    class="text-[13px] text-[#86868b] italic dark:text-[#98989d]"
                                >
                                    … und {{ config.total_commits - config.commits.length }} weitere
                                    Commits
                                </p>
                            </div>
                        </Transition>
                    </div>
                </div>
            </div>

            <!-- Teaser Card -->
            <Transition
                enter-active-class="transition-all duration-500 ease-out"
                enter-from-class="opacity-0 translate-y-4"
                enter-to-class="opacity-100 translate-y-0"
            >
                <div
                    v-if="showTeaser"
                    class="mt-4 overflow-hidden rounded-2xl bg-gradient-to-br from-[#1d1d1f] to-[#2c2c2e] ring-1 ring-white/[0.06]"
                >
                    <div class="p-6">
                        <div class="mb-3 flex items-center gap-2">
                            <Sparkles class="h-4 w-4 text-[#ff9f0a]" />
                            <span
                                class="text-[11px] font-bold tracking-wider text-[#ff9f0a] uppercase"
                            >
                                AI Ready Training
                            </span>
                        </div>
                        <h3
                            class="mb-2 text-[20px] leading-[1.25] font-bold tracking-[-0.3px] text-white"
                        >
                            {{ config.teaser_headline }}
                        </h3>
                        <p class="mb-4 text-[14px] leading-[1.5] text-white/60">
                            {{ config.teaser_text }}
                        </p>
                        <a
                            href="/ai-ready"
                            class="inline-flex items-center gap-1.5 text-[14px] font-semibold text-[#ff9f0a] transition-opacity hover:opacity-80"
                        >
                            Mehr erfahren
                            <ArrowRight class="h-3.5 w-3.5" />
                        </a>
                    </div>
                </div>
            </Transition>
        </div>

        <!-- Action -->
        <div class="shrink-0 px-5 pb-5">
            <button
                v-if="showTeaser"
                class="flex w-full cursor-pointer items-center justify-center gap-2 rounded-2xl bg-[#007aff] px-4 py-4 text-[17px] font-semibold tracking-[-0.2px] text-white transition-all active:scale-[0.97]"
                @click="$emit('next')"
            >
                Weiter
                <ChevronRight class="h-5 w-5" />
            </button>
        </div>
    </div>
</template>
