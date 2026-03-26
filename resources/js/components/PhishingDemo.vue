<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';
import { Mail, ChevronLeft, AlertTriangle, ExternalLink, Shield, Clock, Paperclip } from 'lucide-vue-next';

type AnimationPhase =
    | 'inbox'
    | 'email-arriving'
    | 'email-visible'
    | 'email-opening'
    | 'email-open'
    | 'annotations-in'
    | 'resetting';

const phase = ref<AnimationPhase>('inbox');
let timerId: ReturnType<typeof setTimeout> | null = null;

const inboxEmails = [
    {
        id: 'e1',
        sender: 'Anna Berger',
        initials: 'AB',
        initialsColor: 'bg-[#5856d6]',
        subject: 'Q2 Budget-Freigabe',
        preview: 'Hi, anbei die finale Version...',
        time: '10:23',
        unread: false,
    },
    {
        id: 'e2',
        sender: 'Lars Hoffmann',
        initials: 'LH',
        initialsColor: 'bg-[#34c759]',
        subject: 'Meeting morgen verschoben',
        preview: 'Kurze Info: Das Team-Meeting...',
        time: '09:45',
        unread: false,
    },
    {
        id: 'e3',
        sender: 'Projektmanagement',
        initials: 'PM',
        initialsColor: 'bg-[#ff9500]',
        subject: 'Sprint Review Protokoll',
        preview: 'Zusammenfassung des heutigen...',
        time: 'gestern',
        unread: false,
    },
];

const phishingEmail = {
    id: 'phish',
    sender: 'Michael Weber - IT-Abteilung',
    email: 'm.weber@contoso-intern.de',
    initials: 'MW',
    initialsColor: 'bg-[#007aff]',
    subject: 'Dringend: Ihr Microsoft 365 Passwort läuft ab',
    preview: 'Ihr Passwort läuft in 24 Stunden ab...',
    time: 'jetzt',
    unread: true,
};

function schedule(fn: () => void, delay: number) {
    timerId = setTimeout(fn, delay);
}

function runLoop() {
    phase.value = 'inbox';

    schedule(() => {
        phase.value = 'email-arriving';

        schedule(() => {
            phase.value = 'email-visible';

            schedule(() => {
                phase.value = 'email-opening';

                // Let the CSS transition play (400ms), then mark as fully open
                schedule(() => {
                    phase.value = 'email-open';

                    schedule(() => {
                        phase.value = 'annotations-in';

                        schedule(() => {
                            phase.value = 'resetting';

                            schedule(() => {
                                runLoop();
                            }, 600);
                        }, 2500);
                    }, 3000);
                }, 450);
            }, 1500);
        }, 500);
    }, 1500);
}

onMounted(() => {
    runLoop();
});

onUnmounted(() => {
    if (timerId) clearTimeout(timerId);
});

const showPhishingInInbox = (p: AnimationPhase) =>
    ['email-arriving', 'email-visible', 'email-opening'].includes(p);

const showFullEmail = (p: AnimationPhase) =>
    ['email-open', 'annotations-in'].includes(p);

const showAnnotations = (p: AnimationPhase) => p === 'annotations-in';
</script>

<template>
    <div
        class="relative mx-auto w-full max-w-[380px] overflow-hidden rounded-2xl bg-[#1c1c1e] ring-1 ring-white/[0.06]"
        :class="phase === 'resetting' ? 'opacity-0 transition-opacity duration-500' : 'opacity-100 transition-opacity duration-500'"
    >
        <!-- ============ INBOX VIEW ============ -->
        <Transition
            enter-active-class="transition-all duration-400 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-all duration-350 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="!showFullEmail(phase)" class="flex flex-col">
                <!-- Inbox header -->
                <div class="flex items-center justify-between border-b border-white/[0.08] px-4 pb-2.5 pt-4">
                    <div class="flex items-center gap-2">
                        <Mail class="h-4.5 w-4.5 text-[#007aff]" />
                        <span class="text-[17px] font-semibold tracking-[-0.3px] text-[#f5f5f7]">Posteingang</span>
                    </div>
                    <span class="text-[13px] text-[#98989d]">3 E-Mails</span>
                </div>

                <!-- Phishing email arriving -->
                <Transition
                    enter-active-class="transition-all duration-400 ease-out"
                    enter-from-class="opacity-0 -translate-y-full max-h-0"
                    enter-to-class="opacity-100 translate-y-0 max-h-24"
                >
                    <div
                        v-if="showPhishingInInbox(phase)"
                        class="border-b border-white/[0.08] bg-[#007aff]/[0.06] px-4 py-3"
                        :class="phase === 'email-opening' ? 'ring-1 ring-[#007aff]/20' : ''"
                    >
                        <div class="flex items-start gap-3">
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full" :class="phishingEmail.initialsColor">
                                <span class="text-[13px] font-semibold text-white">{{ phishingEmail.initials }}</span>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="mb-0.5 flex items-center justify-between gap-2">
                                    <span class="truncate text-[15px] font-semibold text-[#f5f5f7]">{{ phishingEmail.sender }}</span>
                                    <span class="shrink-0 text-[12px] font-medium text-[#007aff]">{{ phishingEmail.time }}</span>
                                </div>
                                <p class="truncate text-[13px] font-medium text-[#f5f5f7]">{{ phishingEmail.subject }}</p>
                                <p class="truncate text-[12px] text-[#98989d]">{{ phishingEmail.preview }}</p>
                            </div>
                            <div class="mt-1 h-2.5 w-2.5 shrink-0 rounded-full bg-[#007aff]" />
                        </div>
                    </div>
                </Transition>

                <!-- Existing emails -->
                <div
                    v-for="(email, index) in inboxEmails"
                    :key="email.id"
                    class="px-4 py-3"
                    :class="index < inboxEmails.length - 1 ? 'border-b border-white/[0.06]' : ''"
                >
                    <div class="flex items-start gap-3">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full" :class="email.initialsColor">
                            <span class="text-[13px] font-semibold text-white">{{ email.initials }}</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="mb-0.5 flex items-center justify-between gap-2">
                                <span class="truncate text-[14px] text-[#c7c7cc]">{{ email.sender }}</span>
                                <span class="shrink-0 text-[12px] text-[#636366]">{{ email.time }}</span>
                            </div>
                            <p class="truncate text-[13px] text-[#98989d]">{{ email.subject }}</p>
                            <p class="truncate text-[12px] text-[#636366]">{{ email.preview }}</p>
                        </div>
                    </div>
                </div>

                <!-- Bottom padding -->
                <div class="h-3" />
            </div>
        </Transition>

        <!-- ============ FULL EMAIL VIEW ============ -->
        <Transition
            enter-active-class="transition-all duration-400 ease-out"
            enter-from-class="opacity-0 translate-y-4"
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition-all duration-300 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="showFullEmail(phase)" class="flex flex-col">
                <!-- Email header bar -->
                <div class="flex items-center gap-2 border-b border-white/[0.08] px-3 pb-2.5 pt-3">
                    <ChevronLeft class="h-5 w-5 text-[#007aff]" />
                    <span class="text-[15px] text-[#007aff]">Posteingang</span>
                </div>

                <div class="px-4 pt-4 pb-2">
                    <!-- Subject -->
                    <h3 class="mb-3 text-[17px] leading-[1.3] font-semibold tracking-[-0.2px] text-[#f5f5f7]">
                        {{ phishingEmail.subject }}
                    </h3>

                    <!-- Sender info -->
                    <div class="mb-4 flex items-start gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[#007aff]">
                            <span class="text-[14px] font-semibold text-white">MW</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-[14px] font-semibold text-[#f5f5f7]">{{ phishingEmail.sender }}</p>
                            <div class="relative">
                                <p class="text-[12px] text-[#98989d]">
                                    {{ phishingEmail.email }}
                                </p>
                                <!-- Annotation: sender domain -->
                                <Transition
                                    enter-active-class="transition-all duration-500 ease-out"
                                    enter-from-class="opacity-0 scale-95"
                                    enter-to-class="opacity-100 scale-100"
                                >
                                    <div v-if="showAnnotations(phase)" class="absolute -bottom-5 left-0 flex items-center gap-1">
                                        <AlertTriangle class="h-3 w-3 text-[#ff9500]" />
                                        <span class="text-[10px] font-medium text-[#ff9500]">Nicht @contoso.de!</span>
                                    </div>
                                </Transition>
                            </div>
                            <p class="mt-0.5 text-[11px] text-[#636366]">An: mich</p>
                        </div>
                        <span class="mt-0.5 shrink-0 text-[12px] text-[#636366]">11:02</span>
                    </div>
                </div>

                <!-- Email body -->
                <div class="border-t border-white/[0.06] px-4 pt-3 pb-4">
                    <div class="text-[13px] leading-[1.65] text-[#c7c7cc]">
                        <p class="mb-3">Sehr geehrte/r Mitarbeiter/in,</p>
                        <p class="mb-3">
                            Ihr Microsoft 365 Passwort läuft in
                            <span class="font-semibold text-[#f5f5f7]">weniger als 24 Stunden</span>
                            ab. Um eine Unterbrechung Ihres Zugangs zu verhindern, aktualisieren Sie Ihr Passwort bitte umgehend.
                        </p>
                        <p class="mb-4">
                            Bitte nutzen Sie den folgenden Link, um Ihr Passwort sicher zu erneuern:
                        </p>

                        <!-- CTA Button -->
                        <div class="relative mb-4">
                            <div class="inline-flex items-center gap-2 rounded-lg bg-[#0078d4] px-5 py-2.5 text-[14px] font-semibold text-white">
                                <Shield class="h-4 w-4" />
                                Passwort jetzt aktualisieren
                            </div>

                            <!-- Annotation: suspicious URL -->
                            <Transition
                                enter-active-class="transition-all duration-500 ease-out delay-200"
                                enter-from-class="opacity-0 translate-y-1"
                                enter-to-class="opacity-100 translate-y-0"
                            >
                                <div v-if="showAnnotations(phase)" class="mt-1.5 flex items-center gap-1.5 rounded-md bg-[#ff3b30]/10 px-2.5 py-1.5">
                                    <ExternalLink class="h-3 w-3 shrink-0 text-[#ff3b30]" />
                                    <span class="text-[10px] leading-tight font-medium text-[#ff3b30]">
                                        https://m365-security-update.click/reset
                                    </span>
                                    <AlertTriangle class="h-3 w-3 shrink-0 text-[#ff3b30]" />
                                </div>
                            </Transition>
                        </div>

                        <p class="mb-3 text-[12px] text-[#636366]">
                            Falls Sie Ihr Passwort nicht innerhalb der nächsten 24 Stunden aktualisieren, wird Ihr Konto aus Sicherheitsgründen vorübergehend gesperrt.
                        </p>
                        <p class="text-[12px] text-[#636366]">
                            Mit freundlichen Grüßen,<br />
                            Michael Weber<br />
                            IT-Sicherheit &amp; Systemadministration
                        </p>
                    </div>
                </div>

                <!-- Annotation summary badge -->
                <Transition
                    enter-active-class="transition-all duration-600 ease-out delay-400"
                    enter-from-class="opacity-0 translate-y-2"
                    enter-to-class="opacity-100 translate-y-0"
                >
                    <div
                        v-if="showAnnotations(phase)"
                        class="mx-4 mb-4 flex items-center gap-2 rounded-xl bg-[#ff3b30]/8 px-3.5 py-2.5 ring-1 ring-[#ff3b30]/15"
                    >
                        <AlertTriangle class="h-4 w-4 shrink-0 text-[#ff3b30]" />
                        <span class="text-[12px] leading-snug font-medium text-[#ff3b30]/90">
                            Spear-Phishing erkannt: Gefälschte Absenderdomain, verdächtige URL, künstlicher Zeitdruck
                        </span>
                    </div>
                </Transition>
            </div>
        </Transition>
    </div>
</template>
