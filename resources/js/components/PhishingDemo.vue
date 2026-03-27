<script setup lang="ts">
import { ref, onUnmounted } from 'vue';
import {
    Mail,
    ChevronLeft,
    AlertTriangle,
    ExternalLink,
    Shield,
} from 'lucide-vue-next';

type AnimationPhase =
    | 'idle'
    | 'inbox'
    | 'email-arriving'
    | 'email-visible'
    | 'email-opening'
    | 'email-open'
    | 'annotations-in';

const phase = ref<AnimationPhase>('idle');
const containerRef = ref<HTMLElement | null>(null);
let timerId: ReturnType<typeof setTimeout> | null = null;
let observerInstance: IntersectionObserver | null = null;
let hasStarted = false;

const inboxEmails = [
    {
        id: 'e1',
        sender: 'Anna Berger',
        initials: 'AB',
        initialsColor: 'bg-[#5856d6]',
        subject: 'Q2 Budget-Freigabe',
        preview: 'Hi, anbei die finale Version...',
        time: '10:23',
    },
    {
        id: 'e2',
        sender: 'Lars Hoffmann',
        initials: 'LH',
        initialsColor: 'bg-[#34c759]',
        subject: 'Meeting morgen verschoben',
        preview: 'Kurze Info: Das Team-Meeting...',
        time: '09:45',
    },
    {
        id: 'e3',
        sender: 'Projektmanagement',
        initials: 'PM',
        initialsColor: 'bg-[#ff9500]',
        subject: 'Sprint Review Protokoll',
        preview: 'Zusammenfassung des heutigen...',
        time: 'gestern',
    },
];

const phishingEmail = {
    sender: 'Michael Weber - IT-Abteilung',
    email: 'm.weber@contoso-intern.de',
    initials: 'MW',
    subject: 'Dringend: Ihr Microsoft 365 Passwort läuft ab',
    preview: 'Ihr Passwort läuft in 24 Stunden ab...',
    time: 'jetzt',
};

function schedule(fn: () => void, delay: number) {
    timerId = setTimeout(fn, delay);
}

function runOnce() {
    phase.value = 'inbox';

    schedule(() => {
        phase.value = 'email-arriving';
        schedule(() => {
            phase.value = 'email-visible';
            schedule(() => {
                phase.value = 'email-opening';
                schedule(() => {
                    phase.value = 'email-open';
                    schedule(() => {
                        phase.value = 'annotations-in';
                    }, 3000);
                }, 600);
            }, 2500);
        }, 800);
    }, 2000);
}

function onVisible(el: HTMLElement) {
    observerInstance = new IntersectionObserver(
        (entries) => {
            if (entries[0].isIntersecting && !hasStarted) {
                hasStarted = true;
                runOnce();
                observerInstance?.disconnect();
            }
        },
        { threshold: 0.3 },
    );
    observerInstance.observe(el);
}

function setRef(el: unknown) {
    const htmlEl = el as HTMLElement | null;
    containerRef.value = htmlEl;
    if (htmlEl) onVisible(htmlEl);
}

onUnmounted(() => {
    if (timerId) clearTimeout(timerId);
    observerInstance?.disconnect();
});

const showPhishingInInbox = (p: AnimationPhase) =>
    ['email-arriving', 'email-visible', 'email-opening'].includes(p);

const showFullEmail = (p: AnimationPhase) =>
    ['email-open', 'annotations-in'].includes(p);

const showAnnotations = (p: AnimationPhase) => p === 'annotations-in';
</script>

<template>
    <div
        :ref="setRef"
        class="relative mx-auto w-full max-w-[380px] overflow-hidden rounded-2xl bg-[#1c1c1e] ring-1 ring-white/[0.06] transition-opacity duration-500"
        :class="phase === 'idle' ? 'opacity-60' : 'opacity-100'"
    >
        <!-- INBOX VIEW -->
        <Transition
            enter-active-class="transition-all duration-400 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-all duration-350 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="!showFullEmail(phase)" class="flex flex-col">
                <div
                    class="flex items-center justify-between border-b border-white/[0.08] px-4 pt-4 pb-2.5"
                >
                    <div class="flex items-center gap-2">
                        <Mail class="h-4.5 w-4.5 text-[#007aff]" />
                        <span
                            class="text-[17px] font-semibold tracking-[-0.3px] text-[#f5f5f7]"
                            >Posteingang</span
                        >
                    </div>
                    <span class="text-[13px] text-[#98989d]">3 E-Mails</span>
                </div>

                <Transition
                    enter-active-class="transition-all duration-400 ease-out"
                    enter-from-class="opacity-0 -translate-y-full max-h-0"
                    enter-to-class="opacity-100 translate-y-0 max-h-24"
                >
                    <div
                        v-if="showPhishingInInbox(phase)"
                        class="border-b border-white/[0.08] bg-[#007aff]/[0.06] px-4 py-3"
                        :class="
                            phase === 'email-opening'
                                ? 'ring-1 ring-[#007aff]/20'
                                : ''
                        "
                    >
                        <div class="flex items-start gap-3">
                            <div
                                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-[#007aff]"
                            >
                                <span
                                    class="text-[13px] font-semibold text-white"
                                    >{{ phishingEmail.initials }}</span
                                >
                            </div>
                            <div class="min-w-0 flex-1">
                                <div
                                    class="mb-0.5 flex items-center justify-between gap-2"
                                >
                                    <span
                                        class="truncate text-[15px] font-semibold text-[#f5f5f7]"
                                        >{{ phishingEmail.sender }}</span
                                    >
                                    <span
                                        class="shrink-0 text-[12px] font-medium text-[#007aff]"
                                        >{{ phishingEmail.time }}</span
                                    >
                                </div>
                                <p
                                    class="truncate text-[13px] font-medium text-[#f5f5f7]"
                                >
                                    {{ phishingEmail.subject }}
                                </p>
                                <p class="truncate text-[12px] text-[#98989d]">
                                    {{ phishingEmail.preview }}
                                </p>
                            </div>
                            <div
                                class="mt-1 h-2.5 w-2.5 shrink-0 rounded-full bg-[#007aff]"
                            />
                        </div>
                    </div>
                </Transition>

                <div
                    v-for="(email, index) in inboxEmails"
                    :key="email.id"
                    class="px-4 py-3"
                    :class="
                        index < inboxEmails.length - 1
                            ? 'border-b border-white/[0.06]'
                            : ''
                    "
                >
                    <div class="flex items-start gap-3">
                        <div
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full"
                            :class="email.initialsColor"
                        >
                            <span
                                class="text-[13px] font-semibold text-white"
                                >{{ email.initials }}</span
                            >
                        </div>
                        <div class="min-w-0 flex-1">
                            <div
                                class="mb-0.5 flex items-center justify-between gap-2"
                            >
                                <span
                                    class="truncate text-[14px] text-[#c7c7cc]"
                                    >{{ email.sender }}</span
                                >
                                <span
                                    class="shrink-0 text-[12px] text-[#636366]"
                                    >{{ email.time }}</span
                                >
                            </div>
                            <p class="truncate text-[13px] text-[#98989d]">
                                {{ email.subject }}
                            </p>
                            <p class="truncate text-[12px] text-[#636366]">
                                {{ email.preview }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="h-3" />
            </div>
        </Transition>

        <!-- FULL EMAIL VIEW -->
        <Transition
            enter-active-class="transition-all duration-400 ease-out"
            enter-from-class="opacity-0 translate-y-4"
            enter-to-class="opacity-100 translate-y-0"
        >
            <div v-if="showFullEmail(phase)" class="flex flex-col">
                <div
                    class="flex items-center gap-2 border-b border-white/[0.08] px-3 pt-3 pb-2.5"
                >
                    <ChevronLeft class="h-5 w-5 text-[#007aff]" />
                    <span class="text-[15px] text-[#007aff]">Posteingang</span>
                </div>

                <div class="px-4 pt-4 pb-2">
                    <h3
                        class="mb-3 text-[17px] leading-[1.3] font-semibold tracking-[-0.2px] text-[#f5f5f7]"
                    >
                        {{ phishingEmail.subject }}
                    </h3>
                    <div class="mb-4 flex items-start gap-3">
                        <div
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[#007aff]"
                        >
                            <span class="text-[14px] font-semibold text-white"
                                >MW</span
                            >
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-[14px] font-semibold text-[#f5f5f7]">
                                {{ phishingEmail.sender }}
                            </p>
                            <div class="relative">
                                <p class="text-[12px] text-[#98989d]">
                                    {{ phishingEmail.email }}
                                </p>
                                <Transition
                                    enter-active-class="transition-all duration-500 ease-out"
                                    enter-from-class="opacity-0 scale-95"
                                    enter-to-class="opacity-100 scale-100"
                                >
                                    <div
                                        v-if="showAnnotations(phase)"
                                        class="absolute -bottom-5 left-0 flex items-center gap-1"
                                    >
                                        <AlertTriangle
                                            class="h-3 w-3 text-[#ff9500]"
                                        />
                                        <span
                                            class="text-[10px] font-medium text-[#ff9500]"
                                            >Nicht @contoso.de!</span
                                        >
                                    </div>
                                </Transition>
                            </div>
                            <p class="mt-0.5 text-[11px] text-[#636366]">
                                An: mich
                            </p>
                        </div>
                        <span class="mt-0.5 shrink-0 text-[12px] text-[#636366]"
                            >11:02</span
                        >
                    </div>
                </div>

                <div class="border-t border-white/[0.06] px-4 pt-3 pb-4">
                    <div class="text-[13px] leading-[1.65] text-[#c7c7cc]">
                        <p class="mb-3">Sehr geehrte/r Mitarbeiter/in,</p>
                        <p class="mb-3">
                            Ihr Microsoft 365 Passwort läuft in
                            <span class="font-semibold text-[#f5f5f7]"
                                >weniger als 24 Stunden</span
                            >
                            ab. Um eine Unterbrechung Ihres Zugangs zu
                            verhindern, aktualisieren Sie Ihr Passwort bitte
                            umgehend.
                        </p>
                        <p class="mb-4">
                            Bitte nutzen Sie den folgenden Link, um Ihr Passwort
                            sicher zu erneuern:
                        </p>

                        <div class="relative mb-4">
                            <div
                                class="inline-flex items-center gap-2 rounded-lg bg-[#0078d4] px-5 py-2.5 text-[14px] font-semibold text-white"
                            >
                                <Shield class="h-4 w-4" />
                                Passwort jetzt aktualisieren
                            </div>
                            <Transition
                                enter-active-class="transition-all duration-500 ease-out delay-200"
                                enter-from-class="opacity-0 translate-y-1"
                                enter-to-class="opacity-100 translate-y-0"
                            >
                                <div
                                    v-if="showAnnotations(phase)"
                                    class="mt-1.5 flex items-center gap-1.5 rounded-md bg-[#ff3b30]/10 px-2.5 py-1.5"
                                >
                                    <ExternalLink
                                        class="h-3 w-3 shrink-0 text-[#ff3b30]"
                                    />
                                    <span
                                        class="text-[10px] leading-tight font-medium text-[#ff3b30]"
                                    >
                                        https://m365-security-update.click/reset
                                    </span>
                                    <AlertTriangle
                                        class="h-3 w-3 shrink-0 text-[#ff3b30]"
                                    />
                                </div>
                            </Transition>
                        </div>

                        <p class="mb-3 text-[12px] text-[#636366]">
                            Falls Sie Ihr Passwort nicht innerhalb der nächsten
                            24 Stunden aktualisieren, wird Ihr Konto aus
                            Sicherheitsgründen vorübergehend gesperrt.
                        </p>
                        <p class="text-[12px] text-[#636366]">
                            Mit freundlichen Grüßen,<br />
                            Michael Weber<br />
                            IT-Sicherheit &amp; Systemadministration
                        </p>
                    </div>
                </div>

                <Transition
                    enter-active-class="transition-all duration-600 ease-out delay-400"
                    enter-from-class="opacity-0 translate-y-2"
                    enter-to-class="opacity-100 translate-y-0"
                >
                    <div
                        v-if="showAnnotations(phase)"
                        class="mx-4 mb-4 flex items-center gap-2 rounded-xl bg-[#ff3b30]/8 px-3.5 py-2.5 ring-1 ring-[#ff3b30]/15"
                    >
                        <AlertTriangle
                            class="h-4 w-4 shrink-0 text-[#ff3b30]"
                        />
                        <span
                            class="text-[12px] leading-snug font-medium text-[#ff3b30]/90"
                        >
                            Spear-Phishing erkannt: Gefälschte Absenderdomain,
                            verdächtige URL, künstlicher Zeitdruck
                        </span>
                    </div>
                </Transition>
            </div>
        </Transition>
    </div>
</template>
