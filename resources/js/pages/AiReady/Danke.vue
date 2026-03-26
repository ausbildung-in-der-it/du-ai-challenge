<script setup lang="ts">
import { Check, ArrowRight } from 'lucide-vue-next';
</script>

<template>
    <div class="flex min-h-dvh flex-col items-center justify-center bg-black px-6 font-sans text-[#f5f5f7] antialiased">
        <div class="max-w-lg text-center">
            <div class="mb-8 inline-flex h-20 w-20 items-center justify-center rounded-full bg-[#34c759]/15">
                <Check class="h-10 w-10 text-[#34c759]" />
            </div>

            <h1 class="mb-4 text-[clamp(28px,5vw,40px)] leading-[1.12] font-bold tracking-[-1px]">
                Willkommen bei AI Ready.
            </h1>

            <p class="mb-6 text-[clamp(17px,2vw,20px)] leading-[1.6] text-white/50">
                Deine Buchung ist eingegangen. Du bist einer von 10 Early-Access-Teilnehmern.
            </p>

            <div class="mb-10 rounded-2xl bg-[#1c1c1e] p-6 text-left ring-1 ring-white/[0.06]">
                <p class="mb-4 text-[15px] font-semibold">Was passiert jetzt?</p>
                <ul class="space-y-3 text-[15px] leading-[1.55] text-white/50">
                    <li class="flex gap-3">
                        <span class="shrink-0 text-[#34c759]">1.</span>
                        Du bekommst eine Bestätigung per E-Mail mit Rechnung.
                    </li>
                    <li class="flex gap-3">
                        <span class="shrink-0 text-[#34c759]">2.</span>
                        Noel meldet sich persönlich bei dir, um den Workshop-Termin abzustimmen.
                    </li>
                    <li class="flex gap-3">
                        <span class="shrink-0 text-[#34c759]">3.</span>
                        Ab 1. Juni 2026 bekommt dein Team Zugang zu allen 7 Modulen.
                    </li>
                </ul>
            </div>

            <div class="space-y-4">
                <a
                    href="/journey/real-or-fake"
                    class="group inline-flex items-center gap-2 text-[17px] font-medium text-[#007aff] transition-colors hover:text-[#007aff]/80"
                >
                    In der Zwischenzeit: Probier das Quiz aus
                    <ArrowRight class="h-4 w-4 transition-transform group-hover:translate-x-0.5" />
                </a>
            </div>

            <p class="mt-12 text-[14px] text-white/20">
                Fragen?
                <a href="mailto:noel@ausbildung-in-der-it.de" class="text-white/30 transition-colors hover:text-white/50">
                    noel@ausbildung-in-der-it.de
                </a>
            </p>
        </div>
    </div>
</template>
