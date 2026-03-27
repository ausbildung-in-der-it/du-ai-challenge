<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { X, Gift, ArrowRight, Trophy, Users, Sparkles } from 'lucide-vue-next';
import { index as aiReadyIndex } from '@/actions/App/Http/Controllers/AiReadyController';

defineProps<{
    open: boolean;
}>();

defineEmits<{
    close: [];
}>();
</script>

<template>
    <Teleport to="body">
        <div
            v-if="open"
            class="fixed inset-0 z-50 flex items-end bg-black/65 p-3 backdrop-blur-sm sm:items-center sm:justify-center sm:p-6"
            @click="$emit('close')"
        >
            <div
                class="w-full max-h-[90dvh] max-w-2xl overflow-y-auto rounded-[28px] bg-[#101114] text-[#f5f5f7] shadow-2xl ring-1 ring-white/10"
                @click.stop
            >
                <div
                    class="flex items-start justify-between border-b border-white/10 px-6 py-5 sm:px-8"
                >
                    <div>
                        <p
                            class="text-[12px] font-semibold tracking-[0.18em] text-[#34c759] uppercase"
                        >
                            Gewinnspiel
                        </p>
                        <h2
                            class="mt-2 text-[26px] leading-[1.05] font-bold tracking-[-0.8px]"
                        >
                            1 Platz für euer AI Training + Workshop
                        </h2>
                    </div>
                    <button
                        type="button"
                        class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-white/5 text-white/60 transition hover:bg-white/10 hover:text-white"
                        @click="$emit('close')"
                    >
                        <X class="h-5 w-5" />
                    </button>
                </div>

                <div class="space-y-6 px-6 py-6 sm:px-8 sm:py-8">
                    <div class="grid gap-3 sm:grid-cols-3">
                        <div
                            class="rounded-2xl bg-white/5 p-4 ring-1 ring-white/8"
                        >
                            <Gift class="h-5 w-5 text-[#34c759]" />
                            <p
                                class="mt-3 text-[12px] font-semibold tracking-[0.14em] text-white/45 uppercase"
                            >
                                Gewinn
                            </p>
                            <p class="mt-1 text-[16px] font-semibold">
                                AI Training + Workshop im Wert von 1.990 € netto
                            </p>
                        </div>
                        <div
                            class="rounded-2xl bg-white/5 p-4 ring-1 ring-white/8"
                        >
                            <Trophy class="h-5 w-5 text-[#ff9f0a]" />
                            <p
                                class="mt-3 text-[12px] font-semibold tracking-[0.14em] text-white/45 uppercase"
                            >
                                Teilnahme
                            </p>
                            <p class="mt-1 text-[16px] font-semibold">
                                Challenge in 5 bis 10 Minuten durchspielen
                            </p>
                        </div>
                        <div
                            class="rounded-2xl bg-white/5 p-4 ring-1 ring-white/8"
                        >
                            <Users class="h-5 w-5 text-[#007aff]" />
                            <p
                                class="mt-3 text-[12px] font-semibold tracking-[0.14em] text-white/45 uppercase"
                            >
                                Für wen
                            </p>
                            <p class="mt-1 text-[16px] font-semibold">
                                Für dich und dein Unternehmen
                            </p>
                        </div>
                    </div>

                    <div
                        class="rounded-[24px] bg-gradient-to-br from-[#17181c] to-[#0b0c0f] p-5 ring-1 ring-white/8 sm:p-6"
                    >
                        <p class="text-[18px] leading-[1.55] text-white/78">
                            Du spielst die Challenge, trägst am Ende deine
                            E-Mail-Adresse ein und landest im Lostopf. Optional
                            kannst du dein LinkedIn-Profil hinterlegen, damit
                            wir dich leichter zuordnen.
                        </p>
                        <p class="mt-4 text-[15px] leading-[1.6] text-white/48">
                            Wenn du das Training direkt buchst und gleichzeitig
                            gewinnst, erstatten wir den Kauf vollständig zurück.
                        </p>
                    </div>

                    <div
                        class="rounded-2xl bg-[#081b34] p-5 ring-1 ring-[#007aff]/25"
                    >
                        <div class="flex items-start gap-3">
                            <Sparkles
                                class="mt-0.5 h-5 w-5 shrink-0 text-[#66b3ff]"
                            />
                            <p class="text-[15px] leading-[1.6] text-[#d8ebff]">
                                Das Training ist kein Vortrag. Dein Team
                                arbeitet hands-on mit echten KI-Szenarien, baut
                                ein Gefühl für Qualität, Risiko und Prompting
                                auf und geht mit einem klaren Vorgehen für den
                                Einsatz im Unternehmen raus.
                            </p>
                        </div>
                    </div>

                    <div
                        class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
                    >
                        <p class="text-[13px] text-white/35">
                            Double Opt-ins verschicken wir separat. Heute
                            sammeln wir nur die Einträge.
                        </p>
                        <Link
                            :href="aiReadyIndex()"
                            class="inline-flex items-center justify-center gap-2 rounded-full bg-white px-5 py-3 text-[15px] font-semibold text-[#101114] transition hover:bg-white/90"
                        >
                            Mehr zum Training
                            <ArrowRight class="h-4 w-4" />
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </Teleport>
</template>
