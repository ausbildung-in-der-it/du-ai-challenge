<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Zap, ArrowRight, Sparkles, Presentation, Clock, MapPin } from 'lucide-vue-next';
</script>

<template>
    <Head title="d:u26 — AI Challenge" />
    <div class="flex min-h-dvh flex-col items-center justify-center bg-black px-7 font-sans text-[#f5f5f7] antialiased sm:px-8">
        <div class="w-full max-w-md">
            <p class="mb-10 text-center text-[13px] font-medium tracking-[0.1em] text-white/20 uppercase">
                Data Unplugged 2026
            </p>

            <div class="space-y-4">
                <!-- Challenge / Quiz -->
                <a
                    href="/journey/real-or-fake"
                    class="group flex gap-5 rounded-2xl bg-[#1c1c1e] p-5 ring-1 ring-white/[0.06] transition-all hover:ring-white/[0.12] active:scale-[0.98] sm:p-6"
                >
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-[14px] bg-gradient-to-b from-[#007aff] to-[#0055d4] shadow-lg shadow-[#007aff]/20">
                        <Zap class="h-6 w-6 text-white" />
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="mb-1 flex items-center justify-between">
                            <h2 class="text-[17px] font-semibold tracking-[-0.2px]">Real or Fake?</h2>
                            <ArrowRight class="h-4 w-4 text-white/25 transition-transform group-hover:translate-x-0.5 group-hover:text-white/50" />
                        </div>
                        <p class="text-[14px] leading-[1.5] text-white/45">
                            KI-Storys, die zu absurd klingen, um wahr zu sein. Oder doch nicht? Die d:u26 Challenge.
                        </p>
                    </div>
                </a>

                <!-- Talk Ankündigung -->
                <div class="rounded-2xl bg-[#1c1c1e] p-5 ring-1 ring-white/[0.06] sm:p-6">
                    <div class="mb-3 flex items-center gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#ff9f0a]/10">
                            <Presentation class="h-5 w-5 text-[#ff9f0a]" />
                        </div>
                        <div>
                            <p class="text-[11px] font-bold tracking-wider text-[#ff9f0a] uppercase">Live Talk</p>
                            <h2 class="text-[17px] font-semibold tracking-[-0.2px]">KI-Agenten: Live-Demo</h2>
                        </div>
                    </div>
                    <p class="mb-3 text-[14px] leading-[1.5] text-white/45">
                        Vom Prompt zum fertigen Feature — live auf der Bühne. Wie ich mit KI-Agenten entwickle und was das für Teams bedeutet.
                    </p>
                    <div class="flex items-center gap-4 text-[13px] text-white/35">
                        <span class="flex items-center gap-1.5">
                            <Clock class="h-3.5 w-3.5" />
                            Heute, 16:00 Uhr
                        </span>
                        <span class="flex items-center gap-1.5">
                            <MapPin class="h-3.5 w-3.5" />
                            Tomorrow Stage
                        </span>
                    </div>
                </div>

                <!-- AI Ready Angebot -->
                <a
                    href="/ai-ready"
                    class="group flex gap-5 rounded-2xl bg-[#1c1c1e] p-5 ring-1 ring-white/[0.06] transition-all hover:ring-white/[0.12] active:scale-[0.98] sm:p-6"
                >
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-[14px] bg-gradient-to-b from-[#34c759] to-[#28a745] shadow-lg shadow-[#34c759]/20">
                        <Sparkles class="h-6 w-6 text-white" />
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="mb-1 flex items-center justify-between">
                            <h2 class="text-[17px] font-semibold tracking-[-0.2px]">AI Ready Training</h2>
                            <ArrowRight class="h-4 w-4 text-white/25 transition-transform group-hover:translate-x-0.5 group-hover:text-white/50" />
                        </div>
                        <p class="text-[14px] leading-[1.5] text-white/45">
                            Dein Team soll eigene KI-Agenten bauen? Hands-on lernen, Chancen und Grenzen einschätzen, KI strategisch einsetzen.
                        </p>
                    </div>
                </a>
            </div>

            <p class="mt-10 text-center text-[12px] text-white/15">
                Noel Lang &middot; ausbildung-in-der-it.de
            </p>
        </div>
    </div>
</template>
