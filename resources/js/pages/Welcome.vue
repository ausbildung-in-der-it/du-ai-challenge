<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import {
    Zap,
    ArrowRight,
    Sparkles,
    Presentation,
    Clock,
    MapPin,
    Trophy,
    CircleHelp,
    CheckCircle2,
} from 'lucide-vue-next';
import { show as journeyShow } from '@/actions/App/Http/Controllers/JourneyController';
import { index as aiReadyIndex } from '@/actions/App/Http/Controllers/AiReadyController';
import NewsletterLeadForm from '@/components/NewsletterLeadForm.vue';
import TrainingGiveawayModal from '@/components/TrainingGiveawayModal.vue';

const isGiveawayModalOpen = ref(false);
</script>

<template>
    <Head title="d:u26 — AI Challenge" />
    <div class="min-h-dvh bg-[#f5f5f7] px-5 py-8 font-sans text-[#1d1d1f] antialiased sm:px-8 sm:py-10 dark:bg-black dark:text-[#f5f5f7]">
        <div class="mx-auto max-w-2xl lg:max-w-5xl">

            <!-- Hero Card: Challenge -->
            <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-black/[0.04] sm:p-8 dark:bg-[#1c1c1e] dark:ring-white/[0.06]">
                <div class="lg:flex lg:items-start lg:gap-10">
                    <div class="flex-1">
                        <div class="inline-flex items-center gap-2 rounded-full border border-[#34c759]/25 bg-[#34c759]/8 px-3 py-1 text-[12px] font-semibold text-[#248a3d] dark:bg-[#34c759]/10 dark:text-[#7ce89d]">
                            <Trophy class="h-3.5 w-3.5" />
                            Verlosung: AI Training + Workshop für dein Team
                        </div>

                        <h1 class="mt-5 text-[30px] leading-[1.06] font-bold tracking-[-0.8px] sm:text-[42px]">
                            Spiel die KI-Challenge.
                            <span class="text-[#007aff]">Gewinn das AI Training für deine Firma.</span>
                        </h1>

                        <p class="mt-5 max-w-lg text-[16px] leading-[1.65] text-[#86868b] sm:text-[18px] dark:text-[#98989d]">
                            Real or Fake? dauert 5 bis 10 Minuten. Wer die Challenge durchspielt
                            und seine E-Mail hinterlässt, landet im Lostopf für ein AI Training
                            plus Workshop im Wert von 1.990 EUR netto.
                        </p>

                        <div class="mt-7 flex flex-col gap-3 sm:flex-row">
                            <Link
                                :href="journeyShow('real-or-fake')"
                                class="inline-flex items-center justify-center gap-2 rounded-full bg-[#007aff] px-7 py-3.5 text-[16px] font-semibold text-white shadow-lg shadow-[#007aff]/20 transition-all active:scale-[0.97]"
                            >
                                Zur Challenge
                                <ArrowRight class="h-4 w-4" />
                            </Link>
                            <button
                                type="button"
                                class="inline-flex items-center justify-center gap-2 rounded-full px-6 py-3.5 text-[16px] font-medium text-[#86868b] ring-1 ring-black/[0.08] transition-all hover:text-[#1d1d1f] active:scale-[0.97] dark:text-[#98989d] dark:ring-white/[0.1] dark:hover:text-white"
                                @click="isGiveawayModalOpen = true"
                            >
                                <CircleHelp class="h-4 w-4" />
                                Mehr erfahren
                            </button>
                        </div>
                        <div class="mt-4 flex items-center gap-2 text-[13px] text-[#ff9f0a]/70">
                            <Presentation class="h-3.5 w-3.5" />
                            <span>Live-Talk: KI-Agenten hands-on erleben · Heute 16:00 Uhr · Tomorrow Stage</span>
                        </div>

                        <div class="mt-7 grid gap-2.5 sm:grid-cols-3">
                            <div class="rounded-xl bg-[#f5f5f7] p-3.5 dark:bg-white/[0.04]">
                                <p class="text-[11px] font-semibold tracking-[0.12em] text-[#86868b] uppercase dark:text-[#98989d]">Dauer</p>
                                <p class="mt-1.5 text-[16px] font-semibold">5 bis 10 Minuten</p>
                            </div>
                            <div class="rounded-xl bg-[#f5f5f7] p-3.5 dark:bg-white/[0.04]">
                                <p class="text-[11px] font-semibold tracking-[0.12em] text-[#86868b] uppercase dark:text-[#98989d]">Ablauf</p>
                                <p class="mt-1.5 text-[16px] font-semibold">Challenge spielen, E-Mail eintragen</p>
                            </div>
                            <div class="rounded-xl bg-[#f5f5f7] p-3.5 dark:bg-white/[0.04]">
                                <p class="text-[11px] font-semibold tracking-[0.12em] text-[#86868b] uppercase dark:text-[#98989d]">Gewinn</p>
                                <p class="mt-1.5 text-[16px] font-semibold">Workshop + Training für euer Team</p>
                            </div>
                        </div>
                    </div>

                    <!-- Newsletter Form (right side on desktop) -->
                    <div class="mt-8 shrink-0 lg:mt-0 lg:w-[340px]">
                        <p class="text-[12px] font-semibold tracking-[0.14em] text-[#007aff] uppercase">
                            Vormerken
                        </p>
                        <h2 class="mt-3 text-[22px] leading-[1.12] font-bold tracking-[-0.5px]">
                            Direkt auf die Liste setzen
                        </h2>
                        <p class="mt-2 text-[14px] leading-[1.55] text-[#86868b] dark:text-[#98989d]">
                            Wenn du nachher die Challenge abschließt, wird dein
                            Eintrag einfach aktualisiert.
                        </p>

                        <div class="mt-5 rounded-xl bg-[#f5f5f7] p-4 ring-1 ring-black/[0.04] dark:bg-white/[0.04] dark:ring-white/[0.06]">
                            <NewsletterLeadForm
                                source="homepage"
                                submit-label="Für Verlosung vormerken"
                                success-title="Vorgemerkt"
                                success-message="Dein Eintrag ist gespeichert. Für die Verlosung jetzt noch die Challenge durchspielen und am Ende dieselbe E-Mail-Adresse eintragen."
                            />
                        </div>

                        <div class="mt-3 flex items-start gap-2.5 rounded-xl bg-[#007aff]/[0.04] p-3 dark:bg-[#007aff]/[0.08]">
                            <CheckCircle2 class="mt-0.5 h-4 w-4 shrink-0 text-[#007aff]" />
                            <p class="text-[13px] leading-[1.5] text-[#86868b] dark:text-[#98989d]">
                                Optional kannst du LinkedIn angeben. Wenn du
                                parallel kaufst und gewinnst, erstatten wir den Kauf zurück.
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Bottom Cards -->
            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                <!-- Talk -->
                <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-black/[0.04] sm:p-6 dark:bg-[#1c1c1e] dark:ring-white/[0.06]">
                    <div class="mb-3 flex items-center gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#ff9f0a]/10">
                            <Presentation class="h-5 w-5 text-[#ff9f0a]" />
                        </div>
                        <div>
                            <p class="text-[11px] font-bold tracking-wider text-[#ff9f0a] uppercase">Live Talk</p>
                            <h2 class="text-[17px] font-semibold tracking-[-0.2px]">KI-Agenten: Live-Demo</h2>
                        </div>
                    </div>
                    <p class="text-[14px] leading-[1.6] text-[#86868b] dark:text-[#98989d]">
                        Vom Prompt zum fertigen Feature. Live auf der Bühne.
                    </p>
                    <div class="mt-3 flex flex-wrap items-center gap-4 text-[13px] text-[#86868b]/70 dark:text-[#98989d]/70">
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

                <!-- AI Ready -->
                <Link
                    :href="aiReadyIndex()"
                    class="group flex gap-4 rounded-2xl bg-white p-5 shadow-sm ring-1 ring-black/[0.04] transition-all hover:ring-black/[0.08] active:scale-[0.99] sm:p-6 dark:bg-[#1c1c1e] dark:ring-white/[0.06] dark:hover:ring-white/[0.12]"
                >
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-[14px] bg-gradient-to-b from-[#34c759] to-[#28a745] shadow-lg shadow-[#34c759]/15">
                        <Sparkles class="h-5 w-5 text-white" />
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="mb-1 flex items-center justify-between gap-3">
                            <h2 class="text-[17px] font-semibold tracking-[-0.2px]">AI Ready Training</h2>
                            <ArrowRight class="h-4 w-4 shrink-0 text-[#86868b]/40 transition-transform group-hover:translate-x-0.5 group-hover:text-[#007aff] dark:text-[#98989d]/40" />
                        </div>
                        <p class="text-[14px] leading-[1.6] text-[#86868b] dark:text-[#98989d]">
                            Hands-on AI-Training für dein Team. 7 Module, Simulationen, EU AI Act Compliance.
                        </p>
                    </div>
                </Link>
            </div>

            <!-- Footer -->
            <div class="mt-8 flex flex-wrap justify-center gap-x-4 gap-y-1.5 text-[12px] text-[#86868b]/50 dark:text-[#98989d]/40">
                <span>Noel Lang</span>
                <span>&middot;</span>
                <a href="https://ausbildung-in-der-it.de" target="_blank" rel="noopener" class="hover:text-[#86868b] dark:hover:text-[#98989d]">ausbildung-in-der-it.de</a>
                <span>&middot;</span>
                <a href="/teilnahmebedingungen" class="hover:text-[#86868b] dark:hover:text-[#98989d]">Teilnahmebedingungen</a>
                <span>&middot;</span>
                <a href="https://ausbildung-in-der-it.de/datenschutz" target="_blank" rel="noopener" class="hover:text-[#86868b] dark:hover:text-[#98989d]">Datenschutz</a>
                <span>&middot;</span>
                <a href="https://ausbildung-in-der-it.de/impressum" target="_blank" rel="noopener" class="hover:text-[#86868b] dark:hover:text-[#98989d]">Impressum</a>
            </div>
        </div>

        <TrainingGiveawayModal
            :open="isGiveawayModalOpen"
            @close="isGiveawayModalOpen = false"
        />
    </div>
</template>
