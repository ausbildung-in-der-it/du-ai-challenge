<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3';
import {
    Brain,
    Shield,
    MessageSquare,
    SearchCheck,
    Building2,
    Bot,
    Mail,
    Eye,
    Lock,
    Check,
    Users,
    GraduationCap,
    Calendar,
    ArrowRight,
    ChevronDown,
} from 'lucide-vue-next';

const visible = ref(new Set<string>());
let observer: IntersectionObserver | null = null;

onMounted(() => {
    observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    visible.value.add(entry.target.id);
                }
            });
        },
        { threshold: 0.12 },
    );
    document.querySelectorAll('[data-animate]').forEach((el) => observer?.observe(el));
});

onUnmounted(() => observer?.disconnect());

function isVisible(id: string) {
    return visible.value.has(id);
}

const isCheckingOut = ref(false);

function checkout() {
    isCheckingOut.value = true;
    router.post('/ai-ready/checkout');
}

const modules = {
    literacy: [
        {
            icon: MessageSquare,
            title: 'Prompt Lab',
            desc: 'Hands-on Chat-Simulation. Effektiv prompten lernen — und verstehen, was passiert, wenn Firmendaten in externe AI-Tools fließen.',
            badge: 'Simulation',
        },
        {
            icon: SearchCheck,
            title: 'AI Fact-Check',
            desc: 'AI-generierte Reports, Texte, Zusammenfassungen — manche korrekt, manche halluziniert. Finde die Fehler.',
            badge: 'Simulation',
        },
        {
            icon: Building2,
            title: 'AI Tools im Unternehmen',
            desc: 'Welche AI-Systeme nutzt ihr schon? Was dürft ihr, was nicht? Shadow AI erkennen. EU AI Act Basics.',
            badge: 'Microlearning',
        },
        {
            icon: Bot,
            title: 'AI Agent Basics',
            desc: 'Einen simplen AI-Agenten konfigurieren — No-Code. Verstehen, was Agents können und wo die Grenzen sind.',
            badge: 'Hands-on',
        },
    ],
    security: [
        {
            icon: Mail,
            title: 'Phishing Inbox',
            desc: 'Realistische E-Mail-Simulation. AI-generierte Spear-Phishing-Mails erkennen und melden — basierend auf echten Szenarien.',
            badge: 'Simulation',
        },
        {
            icon: Eye,
            title: 'Deepfake Awareness',
            desc: 'Deepfake-Videos und Voice-Clones erkennen. Echt oder Deepfake? Inklusive Demo, wie einfach ein Deepfake entsteht.',
            badge: 'Demo + Quiz',
        },
        {
            icon: Lock,
            title: 'AI Data Leaks',
            desc: 'Darf ich diese Daten in ein AI-Tool eingeben? Kundenverträge, Code, Mitarbeiterdaten — jedes Szenario mit Erklärung.',
            badge: 'Simulation',
        },
    ],
};
</script>

<template>
    <div class="min-h-dvh bg-black font-sans text-[#f5f5f7] antialiased">
        <!-- HERO -->
        <section class="flex min-h-dvh flex-col items-center justify-center px-6 text-center">
            <div class="max-w-2xl">
                <p class="mb-6 text-[15px] font-medium tracking-[0.08em] text-white/30 uppercase">
                    AI Literacy &middot; AI Security &middot; EU AI Act
                </p>
                <h1 class="mb-6 text-[clamp(36px,6vw,64px)] leading-[1.08] font-bold tracking-[-1.5px]">
                    Alle reden über KI.<br />
                    <span class="text-[#007aff]">Wir machen euer Team ready.</span>
                </h1>
                <p class="mx-auto mb-10 max-w-lg text-[clamp(17px,2.2vw,21px)] leading-[1.55] text-white/50">
                    7 Hands-on Module. Simulationen mit echtem AI-Backend.
                    Compliance-Zertifikate für den EU AI Act.
                </p>
                <a
                    href="#angebot"
                    class="group inline-flex items-center gap-2.5 rounded-full bg-[#007aff] px-10 py-4 text-[17px] font-semibold tracking-[-0.2px] text-white shadow-lg shadow-[#007aff]/25 transition-all hover:shadow-[#007aff]/40 active:scale-[0.97]"
                >
                    Zum Angebot
                    <ArrowRight class="h-5 w-5 transition-transform group-hover:translate-x-0.5" />
                </a>
            </div>
            <ChevronDown class="absolute bottom-8 h-6 w-6 animate-bounce text-white/20" />
        </section>

        <!-- CONTEXT: DU WARST DABEI -->
        <section
            id="context"
            data-animate
            class="flex min-h-[60vh] flex-col items-center justify-center px-6 text-center transition-all duration-700"
            :class="isVisible('context') ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
        >
            <div class="max-w-xl">
                <p class="mb-8 text-[clamp(22px,3.5vw,36px)] leading-[1.35] font-bold tracking-[-0.5px]">
                    Du hast gesehen, was ein KI-Agent in 20 Minuten bauen kann.
                </p>
                <p class="text-[clamp(17px,2vw,21px)] leading-[1.6] text-white/45">
                    Jetzt stell dir vor, dein ganzes Team versteht das.
                </p>
            </div>
        </section>

        <!-- PROBLEM -->
        <section
            id="problem"
            data-animate
            class="px-6 py-32 transition-all duration-700"
            :class="isVisible('problem') ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
        >
            <div class="mx-auto max-w-2xl space-y-20 text-center">
                <div>
                    <p class="text-[clamp(22px,3vw,32px)] leading-[1.35] font-bold tracking-[-0.5px]">
                        4 von 10 Unternehmen wissen: ihre Mitarbeitenden nutzen private KI-Tools.
                    </p>
                    <p class="mt-4 text-[19px] font-semibold text-white/70">
                        Ohne Freigabe. Ohne Richtlinie.
                    </p>
                    <p class="mt-3 text-[15px] text-white/25">Bitkom, Oktober 2025</p>
                </div>
                <div>
                    <p class="text-[15px] font-medium tracking-[0.08em] text-[#ff9f0a]/60 uppercase">
                        EU AI Act &middot; Artikel 4
                    </p>
                    <p class="mt-4 text-[clamp(22px,3vw,32px)] leading-[1.35] font-bold tracking-[-0.5px]">
                        Die KI-Kompetenzpflicht gilt seit Februar 2025.
                    </p>
                    <p class="mt-4 text-[19px] font-semibold text-[#ff9f0a]">
                        Behördliche Durchsetzung ab August 2026.
                    </p>
                    <p class="mt-2 text-[17px] text-white/35">
                        Das sind noch 5 Monate.
                    </p>
                </div>
            </div>
        </section>

        <!-- WER ICH BIN -->
        <section
            id="about"
            data-animate
            class="px-6 py-32 transition-all duration-700"
            :class="isVisible('about') ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
        >
            <div class="mx-auto max-w-2xl">
                <p class="mb-8 text-[15px] font-medium tracking-[0.08em] text-white/25 uppercase">
                    Wer dahinter steht
                </p>
                <div class="space-y-6 text-[clamp(17px,2vw,20px)] leading-[1.7] text-white/60">
                    <p>
                        Ich bin <span class="font-semibold text-white">Noel Lang</span>.
                        Gründer, Entwickler, Didaktiker.
                    </p>
                    <p>
                        Ich nutze KI-Agents jeden Tag. Nicht als Spielerei —
                        als Werkzeug. Ich baue damit Produkte, schreibe Code,
                        analysiere Finanzdaten, erstelle Lernplattformen.
                    </p>
                    <p>
                        Und ich sehe jeden Tag, wie groß die Lücke ist zwischen dem,
                        was KI heute kann — und dem, was die meisten Teams davon verstehen.
                    </p>
                    <p class="text-white/80">
                        Deshalb baue ich <span class="font-semibold text-white">AI Ready</span>:
                        Kein Frontalunterricht, kein PowerPoint-Quiz.
                        Hands-on Simulationen, in denen dein Team KI wirklich erlebt.
                    </p>
                </div>
            </div>
        </section>

        <!-- WAS IST AI READY -->
        <section
            id="product"
            data-animate
            class="px-6 py-32 transition-all duration-700"
            :class="isVisible('product') ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
        >
            <div class="mx-auto max-w-3xl text-center">
                <h2 class="mb-4 text-[clamp(28px,4vw,44px)] leading-[1.12] font-bold tracking-[-1px]">
                    AI Ready
                </h2>
                <p class="mx-auto mb-4 max-w-lg text-[clamp(17px,2vw,21px)] leading-[1.55] text-white/50">
                    Hands-on AI Literacy &amp; Security Simulationen für Teams.
                </p>
                <p class="mx-auto max-w-lg text-[17px] leading-[1.6] text-white/35">
                    Zwei Tracks, sieben Module. Dein Team lernt nicht nur,
                    was KI kann — sondern wie man sie sicher und effektiv einsetzt.
                </p>
            </div>
        </section>

        <!-- TRACK 1: AI LITERACY -->
        <section
            id="track-literacy"
            data-animate
            class="px-6 py-16 transition-all duration-700"
            :class="isVisible('track-literacy') ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
        >
            <div class="mx-auto max-w-3xl">
                <div class="mb-10 flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#007aff]/15">
                        <Brain class="h-5 w-5 text-[#007aff]" />
                    </div>
                    <div>
                        <h3 class="text-[20px] font-bold tracking-[-0.3px]">Track 1: AI Literacy</h3>
                        <p class="text-[14px] text-white/35">4 Module</p>
                    </div>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div
                        v-for="mod in modules.literacy"
                        :key="mod.title"
                        class="rounded-2xl bg-[#1c1c1e] p-6 ring-1 ring-white/[0.06]"
                    >
                        <div class="mb-4 flex items-center justify-between">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#007aff]/10">
                                <component :is="mod.icon" class="h-5 w-5 text-[#007aff]" />
                            </div>
                            <span class="rounded-full bg-[#007aff]/10 px-2.5 py-1 text-[12px] font-medium text-[#007aff]">
                                {{ mod.badge }}
                            </span>
                        </div>
                        <h4 class="mb-2 text-[17px] font-semibold">{{ mod.title }}</h4>
                        <p class="text-[14px] leading-[1.55] text-white/40">{{ mod.desc }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- TRACK 2: AI SECURITY -->
        <section
            id="track-security"
            data-animate
            class="px-6 py-16 transition-all duration-700"
            :class="isVisible('track-security') ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
        >
            <div class="mx-auto max-w-3xl">
                <div class="mb-10 flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#ff9f0a]/15">
                        <Shield class="h-5 w-5 text-[#ff9f0a]" />
                    </div>
                    <div>
                        <h3 class="text-[20px] font-bold tracking-[-0.3px]">Track 2: AI Security</h3>
                        <p class="text-[14px] text-white/35">3 Module</p>
                    </div>
                </div>
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <div
                        v-for="mod in modules.security"
                        :key="mod.title"
                        class="rounded-2xl bg-[#1c1c1e] p-6 ring-1 ring-white/[0.06]"
                    >
                        <div class="mb-4 flex items-center justify-between">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#ff9f0a]/10">
                                <component :is="mod.icon" class="h-5 w-5 text-[#ff9f0a]" />
                            </div>
                            <span class="rounded-full bg-[#ff9f0a]/10 px-2.5 py-1 text-[12px] font-medium text-[#ff9f0a]">
                                {{ mod.badge }}
                            </span>
                        </div>
                        <h4 class="mb-2 text-[17px] font-semibold">{{ mod.title }}</h4>
                        <p class="text-[14px] leading-[1.55] text-white/40">{{ mod.desc }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- DIFFERENTIATOR -->
        <section
            id="diff"
            data-animate
            class="px-6 py-32 transition-all duration-700"
            :class="isVisible('diff') ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
        >
            <div class="mx-auto max-w-2xl space-y-16 text-center">
                <div>
                    <p class="text-[clamp(22px,3vw,32px)] leading-[1.35] font-bold tracking-[-0.5px]">
                        Kein Microlearning-Video mit Quiz am Ende.
                    </p>
                    <p class="mt-6 text-[19px] leading-[1.6] text-white/50">
                        Eure Mitarbeiter sitzen vor einer Chat-Simulation und prompten.
                        Vor einem Posteingang und erkennen Phishing.
                        Vor AI-Outputs und prüfen Fakten.
                    </p>
                    <p class="mt-6 text-[19px] font-semibold text-white/80">
                        Sie tun etwas. Nicht nur zuschauen.
                    </p>
                </div>
                <div class="rounded-2xl bg-[#1c1c1e] p-8 text-left ring-1 ring-white/[0.06]">
                    <div class="grid gap-6 sm:grid-cols-2">
                        <div>
                            <p class="mb-3 text-[13px] font-medium tracking-[0.05em] text-white/25 uppercase">Typisches E-Learning</p>
                            <ul class="space-y-2 text-[15px] text-white/35">
                                <li>3–5 Minuten Video</li>
                                <li>Multiple-Choice-Quiz</li>
                                <li>Vorgefertigte Inhalte</li>
                                <li>Statisch</li>
                            </ul>
                        </div>
                        <div>
                            <p class="mb-3 text-[13px] font-medium tracking-[0.05em] text-[#007aff] uppercase">AI Ready</p>
                            <ul class="space-y-2 text-[15px] text-white/70">
                                <li>10–20 Minuten Hands-on</li>
                                <li>Echte Simulationen</li>
                                <li>Live AI-Backend</li>
                                <li>Personalisierte Szenarien</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- WAS DU BEKOMMST -->
        <section
            id="included"
            data-animate
            class="px-6 py-32 transition-all duration-700"
            :class="isVisible('included') ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
        >
            <div class="mx-auto max-w-2xl">
                <h2 class="mb-12 text-center text-[clamp(28px,4vw,40px)] leading-[1.12] font-bold tracking-[-1px]">
                    Was du bekommst
                </h2>
                <div class="space-y-6">
                    <div v-for="item in [
                        { icon: Brain, text: 'Zugang zu allen 7 Modulen ab 1. Juni 2026', sub: 'AI Literacy + AI Security, beide Tracks komplett' },
                        { icon: Users, text: 'Unbegrenzte User-Lizenzen für 12 Monate', sub: 'Kein Seat-basiertes Pricing. Dein ganzes Team, ein Preis.' },
                        { icon: GraduationCap, text: 'Halbtägiger Workshop (3 Stunden)', sub: 'Remote oder vor Ort. Gemeinsam die Module durcharbeiten und die interne AI-Policy erarbeiten.' },
                        { icon: Shield, text: 'EU AI Act Compliance-Zertifikate', sub: 'Pro Mitarbeiter. Nachweis für Artikel 4 AI Literacy.' },
                        { icon: Calendar, text: 'Early-Access: Du gestaltest mit', sub: 'Persönlicher Feedback-Kanal. Deine Anforderungen fließen direkt ins Produkt.' },
                    ]" :key="item.text" class="flex gap-5">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#007aff]/10">
                            <component :is="item.icon" class="h-5 w-5 text-[#007aff]" />
                        </div>
                        <div>
                            <p class="text-[17px] font-semibold leading-[1.4]">{{ item.text }}</p>
                            <p class="mt-1 text-[15px] leading-[1.5] text-white/40">{{ item.sub }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- PRICING CTA -->
        <section
            id="angebot"
            data-animate
            class="px-6 py-32 transition-all duration-700"
            :class="isVisible('angebot') ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
        >
            <div class="mx-auto max-w-lg text-center">
                <p class="mb-6 text-[15px] font-medium tracking-[0.08em] text-[#34c759]/60 uppercase">
                    Early Access &middot; Limitiert auf 10 Plätze
                </p>
                <div class="mb-2 text-[clamp(48px,8vw,72px)] leading-none font-bold tracking-[-2px]">
                    1.990 <span class="text-[clamp(24px,4vw,36px)] font-semibold text-white/30">&euro;</span>
                </div>
                <p class="mb-8 text-[17px] text-white/35">
                    zzgl. MwSt. &middot; Start 1. Juni 2026
                </p>

                <button
                    :disabled="isCheckingOut"
                    class="group mb-8 inline-flex w-full max-w-sm cursor-pointer items-center justify-center gap-2.5 rounded-2xl bg-[#007aff] px-10 py-5 text-[19px] font-semibold tracking-[-0.3px] text-white shadow-lg shadow-[#007aff]/25 transition-all hover:shadow-[#007aff]/40 active:scale-[0.97] disabled:opacity-50"
                    @click="checkout"
                >
                    <template v-if="isCheckingOut">
                        <div class="h-5 w-5 animate-spin rounded-full border-2 border-white border-t-transparent" />
                        Weiterleitung...
                    </template>
                    <template v-else>
                        Jetzt Early Access sichern
                        <ArrowRight class="h-5 w-5 transition-transform group-hover:translate-x-0.5" />
                    </template>
                </button>

                <div class="rounded-2xl bg-[#1c1c1e] p-6 text-left ring-1 ring-white/[0.06]">
                    <p class="mb-3 text-[13px] font-medium tracking-[0.05em] text-white/25 uppercase">Zum Vergleich</p>
                    <div class="space-y-2 text-[15px] text-white/40">
                        <p>Fraunhofer AI-Workshop: <span class="text-white/60">590 &euro; / Teilnehmer</span></p>
                        <p>KnowBe4, SoSafe: <span class="text-white/60">Per-Seat-Lizenz, ab 25 &euro; / User / Monat</span></p>
                        <p>Intensive Corporate Trainings: <span class="text-white/60">25.000–50.000 &euro;</span></p>
                    </div>
                    <div class="mt-4 border-t border-white/[0.06] pt-4">
                        <p class="text-[15px] text-white/60">
                            <span class="font-semibold text-white">AI Ready:</span>
                            1.990 &euro; &middot; unbegrenzte User &middot; inkl. Workshop
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- QUIZ BRIDGE -->
        <section
            id="quiz"
            data-animate
            class="px-6 py-24 transition-all duration-700"
            :class="isVisible('quiz') ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
        >
            <div class="mx-auto max-w-lg text-center">
                <p class="mb-4 text-[17px] text-white/35">
                    Du hast unser Quiz auf der Data Unplugged gespielt?
                </p>
                <p class="text-[clamp(20px,3vw,26px)] leading-[1.35] font-semibold tracking-[-0.3px]">
                    In AI Ready bekommt dein Team genau diese Erfahrung.
                    Interaktiv. Mit echtem AI-Feedback. Als komplettes Trainingsprogramm.
                </p>
                <a
                    href="/journey/real-or-fake"
                    class="mt-8 inline-flex items-center gap-2 text-[17px] font-medium text-[#007aff] transition-colors hover:text-[#007aff]/80"
                >
                    Quiz nochmal spielen
                    <ArrowRight class="h-4 w-4" />
                </a>
            </div>
        </section>

        <!-- FINAL CTA -->
        <section class="px-6 pt-16 pb-32">
            <div class="mx-auto max-w-lg text-center">
                <p class="mb-6 text-[clamp(22px,3vw,32px)] leading-[1.35] font-bold tracking-[-0.5px]">
                    Die KI ersetzt keine Kompetenz.<br />
                    <span class="text-[#007aff]">Sie verstärkt sie.</span>
                </p>
                <button
                    :disabled="isCheckingOut"
                    class="group inline-flex cursor-pointer items-center gap-2.5 rounded-full bg-[#007aff] px-10 py-4 text-[17px] font-semibold tracking-[-0.2px] text-white shadow-lg shadow-[#007aff]/25 transition-all hover:shadow-[#007aff]/40 active:scale-[0.97] disabled:opacity-50"
                    @click="checkout"
                >
                    <template v-if="!isCheckingOut">
                        Jetzt sichern &middot; 1.990 &euro;
                        <ArrowRight class="h-5 w-5 transition-transform group-hover:translate-x-0.5" />
                    </template>
                    <template v-else>
                        <div class="h-5 w-5 animate-spin rounded-full border-2 border-white border-t-transparent" />
                    </template>
                </button>
                <p class="mt-4 text-[14px] text-white/20">
                    10 Plätze &middot; zzgl. MwSt. &middot; Start 01.06.2026
                </p>
            </div>
        </section>

        <!-- FOOTER -->
        <footer class="border-t border-white/[0.06] px-6 py-12">
            <div class="mx-auto max-w-2xl text-center text-[14px] text-white/25">
                <p>Noel Lang &middot; Data Unplugged 2026</p>
                <p class="mt-2">
                    <a href="mailto:noel@ausbildung-in-der-it.de" class="transition-colors hover:text-white/40">
                        noel@ausbildung-in-der-it.de
                    </a>
                </p>
            </div>
        </footer>
    </div>
</template>
