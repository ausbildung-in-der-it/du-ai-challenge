<script setup lang="ts">
import {
    Mic,
    MicOff,
    Send,
    Lightbulb,
    Building2,
    Wrench,
    FileText,
    Sparkles,
    AlertCircle,
    RotateCcw,
} from 'lucide-vue-next';
import { ref, computed, onUnmounted } from 'vue';

import { useStreamText } from '@/composables/useStreamText';

const prompt = ref('');
const stream = useStreamText();
const hasGenerated = ref(false);

// --- Preset cards ---
const presets = [
    {
        icon: Building2,
        label: 'Mittelstand & Fachkr\u00e4fte',
        text: 'Wir sind ein mittelst\u00e4ndisches Unternehmen mit 20 Mitarbeitern. Unser gr\u00f6\u00dftes Problem: Wir finden keine Fachkr\u00e4fte f\u00fcr Verwaltung und Kundenservice.',
    },
    {
        icon: Wrench,
        label: 'Handwerk & Angebote',
        text: 'Ich leite die IT in einem Handwerksbetrieb. Unsere Angebotserstellung dauert ewig und Compliance-Dokumentation frisst uns auf.',
    },
    {
        icon: FileText,
        label: 'Steuerberatung',
        text: 'Wir sind eine Steuerberatung. Die Mandantenkorrespondenz und Dokumentensichtung nimmt 50% unserer Zeit ein.',
    },
];

function selectPreset(text: string) {
    prompt.value = text;
}

// --- Audio recording ---
const isRecording = ref(false);
const recordingError = ref<string | null>(null);
const isTranscribing = ref(false);
let mediaRecorder: MediaRecorder | null = null;
let audioChunks: Blob[] = [];
let recordingTimeout: ReturnType<typeof setTimeout> | null = null;

const supportsRecording = computed(() => {
    return (
        typeof navigator !== 'undefined' &&
        !!navigator.mediaDevices?.getUserMedia
    );
});

function getXsrfToken(): string {
    return (
        document.cookie
            .split('; ')
            .find((row) => row.startsWith('XSRF-TOKEN='))
            ?.split('=')[1]
            ?.replace(/%3D/g, '=') ?? ''
    );
}

async function toggleRecording() {
    if (isRecording.value) {
        stopRecording();

        return;
    }

    recordingError.value = null;
    audioChunks = [];

    try {
        const mediaStream = await navigator.mediaDevices.getUserMedia({
            audio: true,
        });
        mediaRecorder = new MediaRecorder(mediaStream, {
            mimeType: MediaRecorder.isTypeSupported('audio/webm')
                ? 'audio/webm'
                : 'audio/mp4',
        });

        mediaRecorder.ondataavailable = (event) => {
            if (event.data.size > 0) {
                audioChunks.push(event.data);
            }
        };

        mediaRecorder.onstop = async () => {
            // Stop all tracks
            mediaStream.getTracks().forEach((track) => track.stop());

            if (audioChunks.length === 0) {
                return;
            }

            const audioBlob = new Blob(audioChunks, {
                type: mediaRecorder?.mimeType ?? 'audio/webm',
            });

            await transcribeAudio(audioBlob);
        };

        mediaRecorder.start();
        isRecording.value = true;

        // Auto-stop after 30 seconds
        recordingTimeout = setTimeout(() => {
            stopRecording();
        }, 30000);
    } catch {
        recordingError.value = 'Mikrofon-Zugriff nicht erlaubt.';
    }
}

function stopRecording() {
    if (recordingTimeout) {
        clearTimeout(recordingTimeout);
        recordingTimeout = null;
    }

    if (mediaRecorder && mediaRecorder.state !== 'inactive') {
        mediaRecorder.stop();
    }

    isRecording.value = false;
}

async function transcribeAudio(blob: Blob) {
    isTranscribing.value = true;
    recordingError.value = null;

    try {
        const formData = new FormData();
        formData.append('audio', blob, 'recording.webm');

        const res = await fetch('/api/ai-ready/transcribe', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                Accept: 'application/json',
                'X-XSRF-TOKEN': getXsrfToken(),
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: formData,
        });

        if (!res.ok) {
            throw new Error(`Transcription failed: ${res.status}`);
        }

        const data = await res.json();

        if (data.text) {
            prompt.value = data.text;
        }
    } catch {
        recordingError.value =
            'Transkription fehlgeschlagen. Bitte versuche es erneut.';
    } finally {
        isTranscribing.value = false;
    }
}

onUnmounted(() => {
    if (isRecording.value) {
        stopRecording();
    }
});

// --- Generation ---
const canSubmit = computed(() => {
    return (
        prompt.value.trim().length >= 10 &&
        !stream.isLoading.value &&
        !isTranscribing.value
    );
});

async function generate() {
    if (!canSubmit.value) {
        return;
    }

    hasGenerated.value = true;
    await stream.stream('/api/ai-ready/generate', {
        prompt: prompt.value.trim(),
    });
}

function reset() {
    stream.reset();
    prompt.value = '';
    hasGenerated.value = false;
}

// --- Simple markdown rendering ---
function renderMarkdown(text: string): string {
    const html = text
        // Escape HTML
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        // Bold
        .replace(
            /\*\*(.+?)\*\*/g,
            '<strong class="text-white/90 font-semibold">$1</strong>',
        )
        // Italic
        .replace(/(?<!\*)\*(?!\*)(.+?)(?<!\*)\*(?!\*)/g, '<em>$1</em>')
        // Headings
        .replace(
            /^### (.+)$/gm,
            '<h4 class="text-[16px] font-semibold text-white/85 mt-5 mb-2">$1</h4>',
        )
        .replace(
            /^## (.+)$/gm,
            '<h3 class="text-[18px] font-bold text-white/90 mt-6 mb-2">$1</h3>',
        )
        // Unordered list items
        .replace(
            /^[-*] (.+)$/gm,
            '<li class="flex gap-2 items-start"><span class="text-[#007aff] mt-1.5 shrink-0 text-[8px]">●</span><span>$1</span></li>',
        )
        // Ordered list items
        .replace(
            /^(\d+)\. (.+)$/gm,
            '<li class="flex gap-2 items-start"><span class="text-[#007aff] font-semibold shrink-0 min-w-[1.2em]">$1.</span><span>$2</span></li>',
        )
        // Horizontal rules
        .replace(/^---$/gm, '<hr class="border-white/[0.08] my-4" />')
        // Line breaks (double newline = paragraph break)
        .replace(/\n\n/g, '</p><p class="mb-3">')
        // Single newlines
        .replace(/\n/g, '<br />');

    return `<p class="mb-3">${html}</p>`;
}
</script>

<template>
    <div class="mx-auto w-full max-w-2xl px-5 sm:px-0">
        <!-- Header -->
        <div class="mb-8 text-center">
            <div
                class="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-[#007aff]/10"
            >
                <Lightbulb class="h-6 w-6 text-[#007aff]" />
            </div>
            <h2
                class="mb-2 text-[22px] leading-[1.25] font-bold tracking-[-0.4px] sm:text-[28px]"
            >
                Was kann KI in deinem Unternehmen bewegen?
            </h2>
            <p class="text-[15px] leading-[1.6] text-white/40 sm:text-[16px]">
                Beschreibe dein Unternehmen oder eine Herausforderung — unsere
                KI zeigt dir konkrete Ideen.
            </p>
        </div>

        <!-- Preset cards -->
        <div v-if="!hasGenerated" class="mb-6">
            <div
                class="flex gap-3 overflow-x-auto pb-2 sm:grid sm:grid-cols-3 sm:overflow-x-visible sm:pb-0"
            >
                <button
                    v-for="(preset, i) in presets"
                    :key="i"
                    class="group flex min-w-[200px] shrink-0 cursor-pointer flex-col gap-3 rounded-2xl bg-[#1c1c1e] p-4 text-left ring-1 ring-white/[0.06] transition-all hover:ring-white/[0.12] active:scale-[0.98] sm:min-w-0"
                    @click="selectPreset(preset.text)"
                >
                    <div
                        class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#007aff]/10"
                    >
                        <component
                            :is="preset.icon"
                            class="h-4.5 w-4.5 text-[#007aff]"
                        />
                    </div>
                    <span
                        class="text-[13px] leading-[1.4] font-medium text-white/50 group-hover:text-white/70"
                    >
                        {{ preset.label }}
                    </span>
                </button>
            </div>
        </div>

        <!-- Input area -->
        <div v-if="!hasGenerated || stream.error.value" class="mb-6">
            <div
                class="rounded-2xl bg-[#1c1c1e] ring-1 ring-white/[0.06] focus-within:ring-white/[0.12]"
            >
                <textarea
                    v-model="prompt"
                    rows="4"
                    placeholder="Beschreibe dein Unternehmen, deine Branche oder eine konkrete Herausforderung..."
                    class="w-full resize-none bg-transparent px-5 pt-5 pb-3 text-[15px] leading-[1.6] text-white/80 placeholder-white/20 outline-none"
                    :disabled="stream.isLoading.value"
                    @keydown.meta.enter="generate"
                    @keydown.ctrl.enter="generate"
                />
                <div class="flex items-center justify-between px-4 pb-4">
                    <div class="flex items-center gap-2">
                        <!-- Mic button -->
                        <button
                            v-if="supportsRecording"
                            :disabled="stream.isLoading.value || isTranscribing"
                            class="flex h-9 w-9 cursor-pointer items-center justify-center rounded-xl transition-all disabled:cursor-not-allowed disabled:opacity-30"
                            :class="
                                isRecording
                                    ? 'bg-[#ff3b30]/15 text-[#ff3b30]'
                                    : 'bg-white/[0.06] text-white/40 hover:bg-white/[0.1] hover:text-white/60'
                            "
                            :title="
                                isRecording
                                    ? 'Aufnahme stoppen'
                                    : 'Spracheingabe'
                            "
                            @click="toggleRecording"
                        >
                            <div v-if="isRecording" class="relative">
                                <Mic class="h-4 w-4" />
                                <span
                                    class="absolute -top-0.5 -right-0.5 h-2 w-2 animate-pulse rounded-full bg-[#ff3b30]"
                                />
                            </div>
                            <MicOff
                                v-else-if="isTranscribing"
                                class="h-4 w-4 animate-pulse"
                            />
                            <Mic v-else class="h-4 w-4" />
                        </button>

                        <!-- Transcribing indicator -->
                        <span
                            v-if="isTranscribing"
                            class="text-[12px] text-white/30"
                        >
                            Transkribiere...
                        </span>

                        <!-- Recording indicator -->
                        <span
                            v-else-if="isRecording"
                            class="text-[12px] text-[#ff3b30]/70"
                        >
                            Aufnahme...
                        </span>
                    </div>

                    <!-- Submit button -->
                    <button
                        :disabled="!canSubmit"
                        class="inline-flex cursor-pointer items-center gap-2 rounded-xl bg-[#007aff] px-4 py-2 text-[14px] font-semibold text-white transition-all hover:bg-[#007aff]/90 active:scale-[0.97] disabled:cursor-not-allowed disabled:opacity-30"
                        @click="generate"
                    >
                        <Send class="h-3.5 w-3.5" />
                        Ideen generieren
                    </button>
                </div>
            </div>

            <!-- Error messages -->
            <div
                v-if="recordingError"
                class="mt-3 flex items-center gap-2 text-[13px] text-[#ff3b30]/70"
            >
                <AlertCircle class="h-3.5 w-3.5 shrink-0" />
                {{ recordingError }}
            </div>
        </div>

        <!-- Streaming response area -->
        <div v-if="hasGenerated" class="mb-6">
            <!-- Loading state -->
            <div
                v-if="stream.isLoading.value && !stream.text.value"
                class="rounded-2xl bg-[#1c1c1e] p-6 ring-1 ring-white/[0.06]"
            >
                <div class="flex items-center gap-3">
                    <div class="flex gap-1">
                        <span
                            class="h-2 w-2 animate-bounce rounded-full bg-[#007aff]/50"
                            style="animation-delay: 0ms"
                        />
                        <span
                            class="h-2 w-2 animate-bounce rounded-full bg-[#007aff]/50"
                            style="animation-delay: 150ms"
                        />
                        <span
                            class="h-2 w-2 animate-bounce rounded-full bg-[#007aff]/50"
                            style="animation-delay: 300ms"
                        />
                    </div>
                    <span class="text-[14px] text-white/30"
                        >KI denkt nach...</span
                    >
                </div>
            </div>

            <!-- Streamed content -->
            <div
                v-if="stream.text.value"
                class="rounded-2xl bg-[#1c1c1e] p-6 ring-1 ring-white/[0.06]"
            >
                <div class="mb-4 flex items-center gap-2">
                    <Sparkles class="h-4 w-4 text-[#007aff]" />
                    <span class="text-[13px] font-medium text-white/40"
                        >KI-Ideen</span
                    >
                    <span
                        v-if="stream.isLoading.value"
                        class="ml-1 inline-block h-3 w-[2px] animate-pulse bg-[#007aff]"
                    />
                </div>
                <!-- eslint-disable-next-line vue/no-v-html -->
                <div
                    class="idea-content text-[14px] leading-[1.7] text-white/60 sm:text-[15px]"
                    v-html="renderMarkdown(stream.text.value)"
                />
            </div>

            <!-- Error state -->
            <div
                v-if="stream.error.value"
                class="mt-4 rounded-2xl bg-[#ff3b30]/5 p-5 ring-1 ring-[#ff3b30]/10"
            >
                <div
                    class="flex items-center gap-2 text-[14px] text-[#ff3b30]/70"
                >
                    <AlertCircle class="h-4 w-4 shrink-0" />
                    Etwas ist schiefgelaufen. Bitte versuche es erneut.
                </div>
            </div>

            <!-- Reset button (shown when done or on error) -->
            <div
                v-if="!stream.isLoading.value"
                class="mt-4 flex justify-center"
            >
                <button
                    class="inline-flex cursor-pointer items-center gap-2 rounded-xl bg-white/[0.06] px-4 py-2.5 text-[14px] font-medium text-white/50 transition-all hover:bg-white/[0.1] hover:text-white/70 active:scale-[0.97]"
                    @click="reset"
                >
                    <RotateCcw class="h-3.5 w-3.5" />
                    Neue Ideen generieren
                </button>
            </div>
        </div>
    </div>
</template>

<style scoped>
.idea-content :deep(li) {
    margin-bottom: 0.4rem;
}

.idea-content :deep(h3),
.idea-content :deep(h4) {
    margin-top: 1.25rem;
    margin-bottom: 0.5rem;
}

.idea-content :deep(h3:first-child),
.idea-content :deep(h4:first-child),
.idea-content :deep(p:first-child) {
    margin-top: 0;
}

.idea-content :deep(hr) {
    margin: 1rem 0;
}
</style>
