<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3';
import {
    ArrowRight,
    Bot,
    Brain,
    Building2,
    Calendar,
    Check,
    ChevronDown,
    Eye,
    FileCheck,
    GraduationCap,
    Handshake,
    Lock,
    Mail,
    MessageSquare,
    Play,
    SearchCheck,
    Shield,
    Sparkles,
    Users,
    Zap,
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
        { threshold: 0.1, rootMargin: '0px 0px -40px 0px' },
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
</script>

<template>
    <div class="min-h-dvh bg-black font-sans text-[#f5f5f7] antialiased">
        <!-- HERO -->
        <section class="relative flex min-h-dvh flex-col items-center justify-center px-5 pt-16 pb-12 text-center sm:px-8">
            <div class="max-w-2xl">
                <p class="mb-5 text-[13px] font-medium tracking-[0.1em] text-white/25 uppercase sm:text-[15px]">
                    AI Literacy &middot; AI Security &middot; EU AI Act
                </p>
                <h1 class="mb-5 text-[32px] leading-[1.1] font-bold tracking-[-1px] sm:text-[48px] lg:text-[60px]">
                    Alle reden über KI.<br />
                    <span class="text-[#007aff]">Wir machen euer Team ready.</span>
                </h1>
                <p class="mx-auto mb-8 max-w-md text-[16px] leading-[1.6] text-white/45 sm:text-[19px]">
                    7 Hands-on Module. Interaktive Simulationen mit echtem AI-Backend.
                    Nicht zuschauen — selber machen.
                </p>
                <div class="flex flex-col items-center gap-4 sm:flex-row sm:justify-center">
                    <a
                        href="#angebot"
                        class="group inline-flex items-center gap-2 rounded-full bg-[#007aff] px-8 py-3.5 text-[16px] font-semibold text-white shadow-lg shadow-[#007aff]/25 transition-all hover:shadow-[#007aff]/40 active:scale-[0.97]"
                    >
                        Zum Angebot
                        <ArrowRight class="h-4 w-4 transition-transform group-hover:translate-x-0.5" />
                    </a>
                    <a
                        href="/journey/real-or-fake"
                        class="group inline-flex items-center gap-2 rounded-full px-8 py-3.5 text-[16px] font-medium text-white/50 ring-1 ring-white/[0.1] transition-all hover:text-white/70 hover:ring-white/[0.2] active:scale-[0.97]"
                    >
                        <Play class="h-4 w-4" />
                        Erst das Quiz ausprobieren
                    </a>
                </div>
            </div>
            <ChevronDown class="absolute bottom-6 h-5 w-5 animate-bounce text-white/15" />
        </section>

        <!-- QUIZ BRIDGE — Für Leute die vom Talk / Quiz kommen -->
        <section
            id="bridge"
            data-animate
            class="px-5 py-20 transition-all duration-700 sm:px-8 sm:py-28"
            :class="isVisible('bridge') ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
        >
            <div class="mx-auto max-w-xl text-center">
                <p class="mb-6 text-[22px] leading-[1.4] font-bold tracking-[-0.3px] sm:text-[30px]">
                    Du warst beim Talk auf der Data Unplugged?
                    Oder hast unser Quiz gespielt?
                </p>
                <p class="text-[16px] leading-[1.65] text-white/45 sm:text-[18px]">
                    Dann weißt du, wie sich das anfühlt: eine KI-Simulation, die dich fordert.
                    AI Ready bringt genau diese Erfahrung als komplettes Trainingsprogramm in dein Unternehmen.
                </p>
                <a
                    href="/journey/real-or-fake"
                    class="mt-6 inline-flex items-center gap-2 text-[15px] font-medium text-[#007aff] transition-colors hover:text-[#007aff]/80"
                >
                    Quiz noch nicht gemacht? Hier ausprobieren
                    <ArrowRight class="h-4 w-4" />
                </a>
            </div>
        </section>

        <!-- DAS PROBLEM -->
        <section
            id="problem"
            data-animate
            class="px-5 py-20 transition-all duration-700 sm:px-8 sm:py-28"
            :class="isVisible('problem') ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
        >
            <div class="mx-auto max-w-2xl space-y-16 text-center sm:space-y-20">
                <div>
                    <p class="text-[22px] leading-[1.35] font-bold tracking-[-0.3px] sm:text-[28px]">
                        4 von 10 Unternehmen wissen: ihre Mitarbeitenden nutzen private KI-Tools.
                    </p>
                    <p class="mt-4 text-[17px] font-semibold text-white/65">
                        Ohne Freigabe. Ohne Richtlinie. Ohne Schulung.
                    </p>
                    <p class="mt-3 text-[13px] text-white/20">Bitkom, Oktober 2025</p>
                </div>
                <div>
                    <p class="text-[13px] font-medium tracking-[0.1em] text-[#ff9f0a]/50 uppercase">
                        EU AI Act &middot; Artikel 4
                    </p>
                    <p class="mt-4 text-[22px] leading-[1.35] font-bold tracking-[-0.3px] sm:text-[28px]">
                        Jeder, der KI-Systeme nutzt oder betreibt, muss sicherstellen, dass sein Personal ausreichend KI-kompetent ist.
                    </p>
                    <p class="mt-5 text-[17px] font-semibold text-[#ff9f0a]">
                        Behördliche Durchsetzung ab August 2026.
                    </p>
                    <p class="mt-2 text-[15px] text-white/30">
                        Das sind noch 5 Monate. Branchenübergreifend, auch für KMU.
                    </p>
                </div>
                <div>
                    <p class="text-[18px] leading-[1.55] text-white/45 sm:text-[20px]">
                        Klassische Schulungen reichen dafür nicht.
                        Ein 5-Minuten-Video mit Multiple-Choice-Quiz macht niemanden KI-kompetent.
                    </p>
                </div>
            </div>
        </section>

        <!-- WER WIR SIND — Authentizität und Credibility -->
        <section
            id="about"
            data-animate
            class="px-5 py-20 transition-all duration-700 sm:px-8 sm:py-28"
            :class="isVisible('about') ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
        >
            <div class="mx-auto max-w-2xl">
                <p class="mb-8 text-[13px] font-medium tracking-[0.1em] text-white/20 uppercase">
                    Wer dahinter steht
                </p>
                <div class="space-y-5 text-[16px] leading-[1.7] text-white/55 sm:text-[18px]">
                    <p>
                        Ich bin <span class="font-semibold text-white">Noel Lang</span>.
                        Gründer, Entwickler, Didaktiker.
                    </p>
                    <p>
                        Ich nutze KI-Agents jeden Tag — nicht als Experiment,
                        als Arbeitswerkzeug. Ich baue damit Produkte, analysiere Daten,
                        automatisiere Prozesse.
                    </p>
                    <p>
                        Und ich baue seit Jahren digitale Lernerfahrungen.
                        Mit meiner Plattform
                        <a href="https://ausbildung-in-der-it.de" target="_blank" rel="noopener" class="font-medium text-[#007aff] underline decoration-[#007aff]/30 underline-offset-2 transition-colors hover:text-[#007aff]/80">
                            ausbildung-in-der-it.de
                        </a>
                        haben wir über <span class="font-semibold text-white">20.000 Lernende</span>
                        durch ihre IHK-Abschlussprüfungen begleitet.
                    </p>
                    <p>
                        Unsere Kunden reichen von Fortune-500-Unternehmen über
                        Landesbehörden bis zum klassischen Systemhaus.
                        <!-- TODO: Kundenlogos oder konkrete Referenzen -->
                    </p>
                    <p class="text-white/75">
                        Diese Erfahrung fließt direkt in <span class="font-semibold text-white">AI Ready</span>:
                        Wir wissen, wie Menschen in Unternehmen lernen — und wie nicht.
                        Deshalb bauen wir keine PowerPoint-Kurse, sondern Simulationen,
                        in denen dein Team KI wirklich erlebt.
                    </p>
                </div>
            </div>
        </section>

        <!-- WAS IST AI READY — Klarer Überblick -->
        <section
            id="product"
            data-animate
            class="px-5 py-20 transition-all duration-700 sm:px-8 sm:py-28"
            :class="isVisible('product') ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
        >
            <div class="mx-auto max-w-2xl text-center">
                <h2 class="mb-5 text-[28px] leading-[1.12] font-bold tracking-[-0.8px] sm:text-[40px]">
                    AI Ready
                </h2>
                <p class="mx-auto mb-6 max-w-md text-[16px] leading-[1.6] text-white/50 sm:text-[19px]">
                    Ein Trainingsprogramm für Teams, die KI nicht nur nutzen,
                    sondern verstehen und verantworten wollen.
                </p>
                <div class="mx-auto max-w-md text-left">
                    <div class="rounded-2xl bg-[#1c1c1e] p-5 ring-1 ring-white/[0.06] sm:p-6">
                        <p class="mb-4 text-[14px] font-medium text-white/30">Konkret heißt das:</p>
                        <ul class="space-y-3 text-[15px] leading-[1.55] text-white/55">
                            <li class="flex gap-3">
                                <Check class="mt-0.5 h-4 w-4 shrink-0 text-[#34c759]" />
                                Dein Team sitzt vor einer Chat-Simulation und lernt, effektiv und sicher zu prompten
                            </li>
                            <li class="flex gap-3">
                                <Check class="mt-0.5 h-4 w-4 shrink-0 text-[#34c759]" />
                                Es erkennt Halluzinationen in AI-generierten Texten
                            </li>
                            <li class="flex gap-3">
                                <Check class="mt-0.5 h-4 w-4 shrink-0 text-[#34c759]" />
                                Es identifiziert AI-Phishing-Mails in einem realistischen Posteingang
                            </li>
                            <li class="flex gap-3">
                                <Check class="mt-0.5 h-4 w-4 shrink-0 text-[#34c759]" />
                                Es versteht, welche Daten in AI-Tools gehören — und welche nicht
                            </li>
                            <li class="flex gap-3">
                                <Check class="mt-0.5 h-4 w-4 shrink-0 text-[#34c759]" />
                                Am Ende steht ein Compliance-Zertifikat für den EU AI Act
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <!-- TRACK 1: AI LITERACY -->
        <section
            id="track1"
            data-animate
            class="px-5 py-16 transition-all duration-700 sm:px-8 sm:py-20"
            :class="isVisible('track1') ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
        >
            <div class="mx-auto max-w-3xl">
                <div class="mb-8 flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#007aff]/15">
                        <Brain class="h-5 w-5 text-[#007aff]" />
                    </div>
                    <div>
                        <h3 class="text-[18px] font-bold tracking-[-0.3px] sm:text-[20px]">Track 1: AI Literacy</h3>
                        <p class="text-[13px] text-white/30">4 Module &middot; KI verstehen und anwenden</p>
                    </div>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <!-- Prompt Lab -->
                    <div class="rounded-2xl bg-[#1c1c1e] p-5 ring-1 ring-white/[0.06] sm:p-6">
                        <div class="mb-4 flex items-center justify-between">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#007aff]/10">
                                <MessageSquare class="h-5 w-5 text-[#007aff]" />
                            </div>
                            <span class="rounded-full bg-[#007aff]/10 px-2.5 py-1 text-[11px] font-medium text-[#007aff]">
                                Hands-on Simulation
                            </span>
                        </div>
                        <h4 class="mb-2 text-[16px] font-semibold">Prompt Lab</h4>
                        <p class="mb-3 text-[14px] leading-[1.55] text-white/40">
                            Eine Chat-UI, die wie ChatGPT aussieht. Geführte Szenarien, in denen dein Team lernt,
                            effektiv zu prompten. Aber auch: was passiert, wenn jemand eine Kundenliste in ein externes AI-Tool kopiert?
                        </p>
                        <p class="text-[13px] leading-[1.5] text-white/25">
                            Das System zeigt live die Konsequenz. Kein Theorie-Slide — ein Erlebnis.
                        </p>
                        <p class="mt-3 text-[12px] text-[#007aff]/60">
                            Zertifikat: AI Prompting &amp; Data Safety
                        </p>
                    </div>

                    <!-- AI Fact-Check -->
                    <div class="rounded-2xl bg-[#1c1c1e] p-5 ring-1 ring-white/[0.06] sm:p-6">
                        <div class="mb-4 flex items-center justify-between">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#007aff]/10">
                                <SearchCheck class="h-5 w-5 text-[#007aff]" />
                            </div>
                            <span class="rounded-full bg-[#007aff]/10 px-2.5 py-1 text-[11px] font-medium text-[#007aff]">
                                Simulation
                            </span>
                        </div>
                        <h4 class="mb-2 text-[16px] font-semibold">AI Fact-Check</h4>
                        <p class="mb-3 text-[14px] leading-[1.55] text-white/40">
                            Dein Team bekommt AI-generierte Reports, Zusammenfassungen und Analysen.
                            Manche sind korrekt, manche enthalten Halluzinationen.
                            Aufgabe: Finde die Fehler, bevor sie in die Entscheidung fließen.
                        </p>
                        <p class="text-[13px] leading-[1.5] text-white/25">
                            Trainiert kritisches Denken und Quellenprüfung — die wichtigste AI-Kompetenz überhaupt.
                        </p>
                        <p class="mt-3 text-[12px] text-[#007aff]/60">
                            Zertifikat: AI Output Verification
                        </p>
                    </div>

                    <!-- AI Tools im Unternehmen -->
                    <div class="rounded-2xl bg-[#1c1c1e] p-5 ring-1 ring-white/[0.06] sm:p-6">
                        <div class="mb-4 flex items-center justify-between">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#007aff]/10">
                                <Building2 class="h-5 w-5 text-[#007aff]" />
                            </div>
                            <span class="rounded-full bg-[#007aff]/10 px-2.5 py-1 text-[11px] font-medium text-[#007aff]">
                                Microlearning
                            </span>
                        </div>
                        <h4 class="mb-2 text-[16px] font-semibold">AI Tools im Unternehmen</h4>
                        <p class="mb-3 text-[14px] leading-[1.55] text-white/40">
                            Welche AI-Systeme sind im Einsatz — Copilot, ChatGPT, Gemini?
                            Was darf man damit, was nicht? Shadow AI erkennen.
                            EU AI Act Risikoklassen verstehen. Firmenrichtlinien einordnen.
                        </p>
                        <p class="text-[13px] leading-[1.5] text-white/25">
                            Auch als SCORM-Export für euer bestehendes LMS verfügbar.
                        </p>
                        <p class="mt-3 text-[12px] text-[#007aff]/60">
                            Zertifikat: AI Governance Fundamentals
                        </p>
                    </div>

                    <!-- AI Agent Basics -->
                    <div class="rounded-2xl bg-[#1c1c1e] p-5 ring-1 ring-white/[0.06] sm:p-6">
                        <div class="mb-4 flex items-center justify-between">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#007aff]/10">
                                <Bot class="h-5 w-5 text-[#007aff]" />
                            </div>
                            <span class="rounded-full bg-[#007aff]/10 px-2.5 py-1 text-[11px] font-medium text-[#007aff]">
                                Hands-on
                            </span>
                        </div>
                        <h4 class="mb-2 text-[16px] font-semibold">AI Agent Basics</h4>
                        <p class="mb-3 text-[14px] leading-[1.55] text-white/40">
                            In einer geführten Sandbox konfiguriert dein Team einen simplen AI-Agenten —
                            z.B. einen E-Mail-Zusammenfasser oder ein Meeting-Protokoll-Tool.
                            Kein Code, alles No-Code.
                        </p>
                        <p class="text-[13px] leading-[1.5] text-white/25">
                            Verstehen, was Agents können, wo die Grenzen liegen, und warum das relevant ist.
                        </p>
                        <p class="mt-3 text-[12px] text-[#007aff]/60">
                            Zertifikat: AI Agent Fundamentals
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- TRACK 2: AI SECURITY -->
        <section
            id="track2"
            data-animate
            class="px-5 py-16 transition-all duration-700 sm:px-8 sm:py-20"
            :class="isVisible('track2') ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
        >
            <div class="mx-auto max-w-3xl">
                <div class="mb-8 flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#ff9f0a]/15">
                        <Shield class="h-5 w-5 text-[#ff9f0a]" />
                    </div>
                    <div>
                        <h3 class="text-[18px] font-bold tracking-[-0.3px] sm:text-[20px]">Track 2: AI Security</h3>
                        <p class="text-[13px] text-white/30">3 Module &middot; KI-Risiken erkennen und abwehren</p>
                    </div>
                </div>
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <!-- Phishing Inbox -->
                    <div class="rounded-2xl bg-[#1c1c1e] p-5 ring-1 ring-white/[0.06] sm:p-6">
                        <div class="mb-4 flex items-center justify-between">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#ff9f0a]/10">
                                <Mail class="h-5 w-5 text-[#ff9f0a]" />
                            </div>
                            <span class="rounded-full bg-[#ff9f0a]/10 px-2.5 py-1 text-[11px] font-medium text-[#ff9f0a]">
                                Hands-on Simulation
                            </span>
                        </div>
                        <h4 class="mb-2 text-[16px] font-semibold">Phishing Inbox</h4>
                        <p class="mb-3 text-[14px] leading-[1.55] text-white/40">
                            Ein Posteingang, der wie Outlook aussieht. Dein Team klassifiziert realistische E-Mails —
                            inklusive AI-generierter Spear-Phishing-Angriffe, die auf echten Szenarien basieren.
                        </p>
                        <p class="text-[13px] leading-[1.5] text-white/25">
                            Der Clou: Die Phishing-Mails werden live per AI generiert.
                            Jeder Durchlauf ist anders.
                        </p>
                        <p class="mt-3 text-[12px] text-[#ff9f0a]/60">
                            Zertifikat: AI Phishing Defense
                        </p>
                    </div>

                    <!-- Deepfake Awareness -->
                    <div class="rounded-2xl bg-[#1c1c1e] p-5 ring-1 ring-white/[0.06] sm:p-6">
                        <div class="mb-4 flex items-center justify-between">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#ff9f0a]/10">
                                <Eye class="h-5 w-5 text-[#ff9f0a]" />
                            </div>
                            <span class="rounded-full bg-[#ff9f0a]/10 px-2.5 py-1 text-[11px] font-medium text-[#ff9f0a]">
                                Demo + Quiz
                            </span>
                        </div>
                        <h4 class="mb-2 text-[16px] font-semibold">Deepfake Awareness</h4>
                        <p class="mb-3 text-[14px] leading-[1.55] text-white/40">
                            Deepfake-Videos und Voice-Clones erkennen: Echt oder generiert?
                            Inklusive Live-Demo, wie schnell ein Deepfake erstellt wird.
                        </p>
                        <p class="text-[13px] leading-[1.5] text-white/25">
                            Dein Team sieht es, versteht die Gefahr, und lernt Verifikationsprozesse.
                        </p>
                        <p class="mt-3 text-[12px] text-[#ff9f0a]/60">
                            Zertifikat: Deepfake Detection
                        </p>
                    </div>

                    <!-- AI Data Leaks -->
                    <div class="rounded-2xl bg-[#1c1c1e] p-5 ring-1 ring-white/[0.06] sm:col-span-2 sm:p-6 lg:col-span-1">
                        <div class="mb-4 flex items-center justify-between">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#ff9f0a]/10">
                                <Lock class="h-5 w-5 text-[#ff9f0a]" />
                            </div>
                            <span class="rounded-full bg-[#ff9f0a]/10 px-2.5 py-1 text-[11px] font-medium text-[#ff9f0a]">
                                Simulation
                            </span>
                        </div>
                        <h4 class="mb-2 text-[16px] font-semibold">AI Data Leaks</h4>
                        <p class="mb-3 text-[14px] leading-[1.55] text-white/40">
                            Szenarien, in denen dein Team entscheidet: Darf ich diese Daten in ein AI-Tool eingeben?
                            Kundenverträge, Finanzdaten, Quellcode, Mitarbeiterdaten.
                        </p>
                        <p class="text-[13px] leading-[1.5] text-white/25">
                            Jedes Szenario mit Erklärung, warum es problematisch wäre — und was stattdessen geht.
                        </p>
                        <p class="mt-3 text-[12px] text-[#ff9f0a]/60">
                            Zertifikat: AI Data Protection
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- WARUM DAS ANDERS IST -->
        <section
            id="diff"
            data-animate
            class="px-5 py-20 transition-all duration-700 sm:px-8 sm:py-28"
            :class="isVisible('diff') ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
        >
            <div class="mx-auto max-w-2xl">
                <div class="mb-12 text-center">
                    <p class="text-[22px] leading-[1.35] font-bold tracking-[-0.3px] sm:text-[28px]">
                        Kein Frontalunterricht.
                        Kein 5-Minuten-Video mit Quiz am Ende.
                    </p>
                    <p class="mt-5 text-[16px] leading-[1.6] text-white/45 sm:text-[18px]">
                        Eure Mitarbeitenden sitzen vor einer Chat-Simulation und prompten.
                        Vor einem Posteingang und erkennen Phishing.
                        Vor AI-Outputs und prüfen Fakten.
                    </p>
                    <p class="mt-5 text-[17px] font-semibold text-white/75">
                        Sie tun etwas. Das ist der Unterschied.
                    </p>
                </div>
                <div class="rounded-2xl bg-[#1c1c1e] p-5 ring-1 ring-white/[0.06] sm:p-8">
                    <div class="grid gap-8 sm:grid-cols-2">
                        <div>
                            <p class="mb-4 text-[12px] font-medium tracking-[0.08em] text-white/20 uppercase">Klassische Schulung</p>
                            <ul class="space-y-3 text-[14px] text-white/30 sm:text-[15px]">
                                <li>3–5 Minuten Video</li>
                                <li>Multiple-Choice-Quiz</li>
                                <li>Vorgefertigte, statische Inhalte</li>
                                <li>Gleich für alle</li>
                                <li>Per-Seat-Lizenz</li>
                            </ul>
                        </div>
                        <div>
                            <p class="mb-4 text-[12px] font-medium tracking-[0.08em] text-[#007aff] uppercase">AI Ready</p>
                            <ul class="space-y-3 text-[14px] text-white/65 sm:text-[15px]">
                                <li>10–20 Minuten Hands-on pro Modul</li>
                                <li>Echte Simulationen mit Live-AI</li>
                                <li>Personalisierte Szenarien, jeder Durchlauf anders</li>
                                <li>SCORM-Export für euer bestehendes LMS</li>
                                <li class="font-semibold text-white/80">Unbegrenzte User, ein Preis</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- WAS DU BEKOMMST — DETAILLIERT -->
        <section
            id="included"
            data-animate
            class="px-5 py-20 transition-all duration-700 sm:px-8 sm:py-28"
            :class="isVisible('included') ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
        >
            <div class="mx-auto max-w-2xl">
                <h2 class="mb-4 text-center text-[24px] leading-[1.2] font-bold tracking-[-0.5px] sm:text-[32px]">
                    Was du konkret bekommst
                </h2>
                <p class="mx-auto mb-10 max-w-md text-center text-[15px] leading-[1.55] text-white/35 sm:mb-12">
                    Kein Kleingedrucktes. Hier steht genau drin, was im Paket ist.
                </p>
                <div class="space-y-8">
                    <!-- 7 Module -->
                    <div class="flex gap-4 sm:gap-5">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#007aff]/10 sm:h-11 sm:w-11">
                            <Brain class="h-5 w-5 text-[#007aff]" />
                        </div>
                        <div>
                            <p class="text-[16px] font-semibold leading-[1.4] sm:text-[17px]">Alle 7 Module ab 1. Juni 2026</p>
                            <p class="mt-1 text-[14px] leading-[1.6] text-white/40 sm:text-[15px]">
                                Beide Tracks komplett: AI Literacy (4 Module) und AI Security (3 Module).
                                Web-App, responsive, funktioniert auf jedem Gerät.
                                Die Module mit statischem Content stehen zusätzlich als SCORM-Paket
                                für euer LMS bereit (Moodle, iSpring, SAP SuccessFactors, etc.).
                            </p>
                        </div>
                    </div>

                    <!-- Unbegrenzte User -->
                    <div class="flex gap-4 sm:gap-5">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#007aff]/10 sm:h-11 sm:w-11">
                            <Users class="h-5 w-5 text-[#007aff]" />
                        </div>
                        <div>
                            <p class="text-[16px] font-semibold leading-[1.4] sm:text-[17px]">Unbegrenzte User-Lizenzen für 12 Monate</p>
                            <p class="mt-1 text-[14px] leading-[1.6] text-white/40 sm:text-[15px]">
                                Kein Per-Seat-Pricing. Ob 5 oder 500 Mitarbeitende — der Preis bleibt gleich.
                                Du kaufst einmal und rollst es aus, ohne bei jeder neuen Person nachzurechnen.
                                So funktioniert Skalierung.
                            </p>
                        </div>
                    </div>

                    <!-- Workshop -->
                    <div class="flex gap-4 sm:gap-5">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#007aff]/10 sm:h-11 sm:w-11">
                            <GraduationCap class="h-5 w-5 text-[#007aff]" />
                        </div>
                        <div>
                            <p class="text-[16px] font-semibold leading-[1.4] sm:text-[17px]">Halbtägiger Workshop (3 Stunden)</p>
                            <p class="mt-1 text-[14px] leading-[1.6] text-white/40 sm:text-[15px]">
                                Remote oder vor Ort, mit mir persönlich. Wir arbeiten die Module
                                gemeinsam mit deinem Team durch und erarbeiten zusammen eure interne AI-Policy.
                                Am Ende habt ihr nicht nur geschulte Mitarbeitende,
                                sondern auch eine dokumentierte Richtlinie.
                            </p>
                        </div>
                    </div>

                    <!-- Zertifikate -->
                    <div class="flex gap-4 sm:gap-5">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#007aff]/10 sm:h-11 sm:w-11">
                            <FileCheck class="h-5 w-5 text-[#007aff]" />
                        </div>
                        <div>
                            <p class="text-[16px] font-semibold leading-[1.4] sm:text-[17px]">EU AI Act Compliance-Zertifikate</p>
                            <p class="mt-1 text-[14px] leading-[1.6] text-white/40 sm:text-[15px]">
                                Jeder Mitarbeitende erhält nach Abschluss ein individuelles Zertifikat
                                als Kompetenznachweis nach Artikel 4 EU AI Act.
                                Das ist der Nachweis, den Behörden ab August 2026 sehen wollen.
                            </p>
                        </div>
                    </div>

                    <!-- Mitwirkung -->
                    <div class="flex gap-4 sm:gap-5">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#34c759]/10 sm:h-11 sm:w-11">
                            <Handshake class="h-5 w-5 text-[#34c759]" />
                        </div>
                        <div>
                            <p class="text-[16px] font-semibold leading-[1.4] sm:text-[17px]">Mitwirkungsphase: Du gestaltest das Produkt mit</p>
                            <p class="mt-1 text-[14px] leading-[1.6] text-white/40 sm:text-[15px]">
                                Wir sind hier transparent: Die Module sind in Entwicklung, der Start ist am 1. Juni 2026.
                                Du sicherst dir jetzt deinen Platz und bekommst einen persönlichen Feedback-Kanal.
                                Deine Anforderungen, deine Branche, deine konkreten Szenarien —
                                sie fließen direkt in die Inhalte ein.
                                Die ersten 10 Kunden formen das Produkt aktiv mit.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- TRANSPARENZ-BOX -->
        <section
            id="transparency"
            data-animate
            class="px-5 py-16 transition-all duration-700 sm:px-8"
            :class="isVisible('transparency') ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
        >
            <div class="mx-auto max-w-2xl">
                <div class="rounded-2xl border border-white/[0.08] bg-[#1c1c1e]/50 p-5 sm:p-8">
                    <div class="mb-4 flex items-center gap-2.5">
                        <Sparkles class="h-5 w-5 text-[#34c759]" />
                        <p class="text-[15px] font-semibold sm:text-[16px]">Ein Wort zur Transparenz</p>
                    </div>
                    <div class="space-y-3 text-[14px] leading-[1.65] text-white/45 sm:text-[15px]">
                        <p>
                            AI Ready ist kein fertiges Produkt, das seit Jahren auf dem Markt ist.
                            Es ist ein neues Trainingsprogramm, das wir gerade bauen — auf Basis unserer
                            langjährigen Erfahrung mit digitalen Lernerfahrungen und unserer täglichen Arbeit mit KI.
                        </p>
                        <p>
                            Dieses Angebot ist bewusst auf 10 Plätze limitiert.
                            Nicht als Marketing-Trick, sondern weil wir mit einer kleinen Gruppe starten wollen,
                            die das Produkt mit uns formt. Eure Rückmeldungen machen AI Ready besser.
                        </p>
                        <p class="text-white/55">
                            Deshalb der Preis: 1.990 &euro; statt dem, was wir später verlangen werden.
                            Ihr bekommt ein besseres Produkt. Wir bekommen echtes Feedback. Fair deal.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- PRICING CTA -->
        <section
            id="angebot"
            data-animate
            class="px-5 py-20 transition-all duration-700 sm:px-8 sm:py-28"
            :class="isVisible('angebot') ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
        >
            <div class="mx-auto max-w-lg text-center">
                <p class="mb-5 text-[13px] font-medium tracking-[0.1em] text-[#34c759]/50 uppercase">
                    Mitwirkungsphase &middot; 10 Plätze &middot; Start 01.06.2026
                </p>
                <div class="mb-2 text-[48px] leading-none font-bold tracking-[-2px] sm:text-[64px]">
                    1.990 <span class="text-[24px] font-semibold text-white/25 sm:text-[32px]">&euro;</span>
                </div>
                <p class="mb-8 text-[15px] text-white/30">
                    zzgl. MwSt. &middot; Einmalzahlung &middot; keine laufenden Kosten
                </p>

                <button
                    :disabled="isCheckingOut"
                    class="group mb-8 inline-flex w-full max-w-sm cursor-pointer items-center justify-center gap-2.5 rounded-2xl bg-[#007aff] px-8 py-4.5 text-[17px] font-semibold text-white shadow-lg shadow-[#007aff]/25 transition-all hover:shadow-[#007aff]/40 active:scale-[0.97] disabled:cursor-not-allowed disabled:opacity-50 sm:text-[18px]"
                    @click="checkout"
                >
                    <template v-if="isCheckingOut">
                        <div class="h-5 w-5 animate-spin rounded-full border-2 border-white border-t-transparent" />
                        Weiterleitung zu Stripe...
                    </template>
                    <template v-else>
                        Jetzt Platz sichern
                        <ArrowRight class="h-5 w-5 transition-transform group-hover:translate-x-0.5" />
                    </template>
                </button>

                <p class="mb-8 text-[13px] text-white/20">
                    Sichere Zahlung über Stripe. Rechnung mit ausgewiesener MwSt.
                </p>

                <div class="rounded-2xl bg-[#1c1c1e] p-5 text-left ring-1 ring-white/[0.06] sm:p-6">
                    <p class="mb-3 text-[12px] font-medium tracking-[0.08em] text-white/20 uppercase">Was das im Vergleich bedeutet</p>
                    <div class="space-y-2.5 text-[14px] text-white/35 sm:text-[15px]">
                        <p>Fraunhofer AI-Kompakteinstieg: <span class="text-white/50">590 &euro; / Teilnehmer</span></p>
                        <p>SoSafe, KnowBe4: <span class="text-white/50">Per-Seat-Lizenz, ab 25 &euro; / User / Monat</span></p>
                        <p>Corporate AI-Trainings: <span class="text-white/50">25.000–50.000 &euro;</span></p>
                    </div>
                    <div class="mt-4 border-t border-white/[0.06] pt-4">
                        <p class="text-[14px] text-white/55 sm:text-[15px]">
                            <span class="font-semibold text-white">AI Ready:</span>
                            1.990 &euro; &middot; unbegrenzte User &middot; 12 Monate &middot; inkl. Workshop
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- WAS DRIN IST — SUMMARY -->
        <section class="px-5 py-12 sm:px-8">
            <div class="mx-auto max-w-lg">
                <div class="rounded-2xl bg-[#1c1c1e] p-5 ring-1 ring-white/[0.06] sm:p-6">
                    <p class="mb-4 text-[14px] font-semibold sm:text-[15px]">Zusammengefasst — das ist drin:</p>
                    <ul class="space-y-2.5 text-[14px] text-white/50 sm:text-[15px]">
                        <li class="flex items-start gap-2.5">
                            <Check class="mt-0.5 h-4 w-4 shrink-0 text-[#34c759]" />
                            7 Module (4&times; AI Literacy + 3&times; AI Security)
                        </li>
                        <li class="flex items-start gap-2.5">
                            <Check class="mt-0.5 h-4 w-4 shrink-0 text-[#34c759]" />
                            Unbegrenzte User-Lizenzen für 12 Monate
                        </li>
                        <li class="flex items-start gap-2.5">
                            <Check class="mt-0.5 h-4 w-4 shrink-0 text-[#34c759]" />
                            Halbtägiger Workshop (3h) — remote oder vor Ort
                        </li>
                        <li class="flex items-start gap-2.5">
                            <Check class="mt-0.5 h-4 w-4 shrink-0 text-[#34c759]" />
                            SCORM-Export für euer bestehendes LMS
                        </li>
                        <li class="flex items-start gap-2.5">
                            <Check class="mt-0.5 h-4 w-4 shrink-0 text-[#34c759]" />
                            EU AI Act Compliance-Zertifikate pro Mitarbeiter
                        </li>
                        <li class="flex items-start gap-2.5">
                            <Check class="mt-0.5 h-4 w-4 shrink-0 text-[#34c759]" />
                            Persönlicher Feedback-Kanal — du formst das Produkt mit
                        </li>
                    </ul>
                </div>
            </div>
        </section>

        <!-- QUIZ AUSPROBIEREN -->
        <section
            id="try-quiz"
            data-animate
            class="px-5 py-20 transition-all duration-700 sm:px-8 sm:py-24"
            :class="isVisible('try-quiz') ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
        >
            <div class="mx-auto max-w-lg text-center">
                <div class="mb-5 inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-[#007aff]/10">
                    <Zap class="h-7 w-7 text-[#007aff]" />
                </div>
                <p class="mb-3 text-[20px] font-bold tracking-[-0.3px] sm:text-[24px]">
                    Willst du erst mal sehen, wie sich das anfühlt?
                </p>
                <p class="mb-6 text-[15px] leading-[1.6] text-white/40 sm:text-[16px]">
                    Unser "Real or Fake"-Quiz gibt dir einen Vorgeschmack.
                    Eine interaktive KI-Simulation, die du in 5 Minuten durchspielen kannst —
                    mit echtem AI-Feedback in Echtzeit.
                </p>
                <a
                    href="/journey/real-or-fake"
                    class="group inline-flex items-center gap-2 rounded-full bg-white/[0.08] px-8 py-3.5 text-[16px] font-semibold text-white transition-all hover:bg-white/[0.12] active:scale-[0.97]"
                >
                    <Play class="h-4 w-4" />
                    Quiz starten
                </a>
            </div>
        </section>

        <!-- FINAL CTA -->
        <section class="px-5 pt-12 pb-24 sm:px-8 sm:pb-32">
            <div class="mx-auto max-w-lg text-center">
                <p class="mb-6 text-[22px] leading-[1.35] font-bold tracking-[-0.3px] sm:text-[28px]">
                    Die KI ersetzt keine Kompetenz.<br />
                    <span class="text-[#007aff]">Sie verstärkt sie.</span>
                </p>
                <p class="mb-8 text-[15px] text-white/35 sm:text-[16px]">
                    Mach dein Team ready — bevor es jemand anderes tut.
                </p>
                <button
                    :disabled="isCheckingOut"
                    class="group inline-flex cursor-pointer items-center gap-2.5 rounded-full bg-[#007aff] px-8 py-3.5 text-[16px] font-semibold text-white shadow-lg shadow-[#007aff]/25 transition-all hover:shadow-[#007aff]/40 active:scale-[0.97] disabled:opacity-50 sm:text-[17px]"
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
                <p class="mt-4 text-[13px] text-white/15">
                    10 Plätze &middot; zzgl. MwSt. &middot; Start 01.06.2026
                </p>
            </div>
        </section>

        <!-- FOOTER -->
        <footer class="border-t border-white/[0.06] px-5 py-10 sm:px-8 sm:py-12">
            <div class="mx-auto max-w-2xl text-center text-[13px] text-white/20 sm:text-[14px]">
                <p class="font-medium text-white/30">Noel Lang</p>
                <p class="mt-1.5">
                    <a href="https://ausbildung-in-der-it.de" target="_blank" rel="noopener" class="transition-colors hover:text-white/40">
                        ausbildung-in-der-it.de
                    </a>
                </p>
                <p class="mt-1.5">
                    <a href="mailto:noel@ausbildung-in-der-it.de" class="transition-colors hover:text-white/40">
                        noel@ausbildung-in-der-it.de
                    </a>
                </p>
                <!-- TODO: Impressum / Datenschutz Links -->
            </div>
        </footer>
    </div>
</template>
