<script setup lang="ts">
import { ref, onUnmounted } from 'vue';
import {
    Mic,
    Square,
    ChevronRight,
    Sparkles,
    AudioLines,
} from 'lucide-vue-next';
import { useStreamText } from '@/composables/useStreamText';

defineEmits<{ next: [] }>();

type Phase = 'intro' | 'recording' | 'transcribing' | 'reacting' | 'done';

const phase = ref<Phase>('intro');
const transcript = ref('');
const aiReaction = useStreamText();
const bars = ref<number[]>(new Array(24).fill(4));
const recordingTime = ref(0);

let mediaRecorder: MediaRecorder | null = null;
let audioChunks: Blob[] = [];
let analyser: AnalyserNode | null = null;
let audioCtx: AudioContext | null = null;
let animFrame: number | null = null;
let timerInterval: ReturnType<typeof setInterval> | null = null;

function getXsrfToken(): string {
    return (
        document.cookie
            .split('; ')
            .find((row) => row.startsWith('XSRF-TOKEN='))
            ?.split('=')[1]
            ?.replace(/%3D/g, '=') ?? ''
    );
}

async function startRecording() {
    try {
        const stream = await navigator.mediaDevices.getUserMedia({
            audio: true,
        });
        phase.value = 'recording';
        audioChunks = [];
        recordingTime.value = 0;

        // Audio analysis for bars
        audioCtx = new AudioContext();
        const source = audioCtx.createMediaStreamSource(stream);
        analyser = audioCtx.createAnalyser();
        analyser.fftSize = 64;
        analyser.smoothingTimeConstant = 0.7;
        source.connect(analyser);
        updateBars();

        // Timer
        timerInterval = setInterval(() => recordingTime.value++, 1000);

        // Use audio/mp4 if available, fallback to audio/webm
        const mimeType = MediaRecorder.isTypeSupported('audio/mp4')
            ? 'audio/mp4'
            : MediaRecorder.isTypeSupported('audio/webm;codecs=opus')
              ? 'audio/webm;codecs=opus'
              : 'audio/webm';

        mediaRecorder = new MediaRecorder(stream, { mimeType });
        mediaRecorder.ondataavailable = (e) => {
            if (e.data.size > 0) audioChunks.push(e.data);
        };
        mediaRecorder.onstop = async () => {
            stream.getTracks().forEach((t) => t.stop());
            cleanup();
            phase.value = 'transcribing';
            await transcribeAudio(mimeType);
        };

        mediaRecorder.start(250); // collect chunks every 250ms
    } catch {
        phase.value = 'done';
        transcript.value =
            '(Mikrofon nicht verfügbar — bitte Berechtigung erteilen)';
    }
}

function stopRecording() {
    mediaRecorder?.stop();
}

function updateBars() {
    if (!analyser) return;

    const data = new Uint8Array(analyser.frequencyBinCount);
    analyser.getByteFrequencyData(data);

    // Map frequency bins to 24 bars
    const newBars: number[] = [];
    const step = Math.floor(data.length / 24);
    for (let i = 0; i < 24; i++) {
        const val = data[i * step] ?? 0;
        // Scale: min 4px, max 48px
        newBars.push(4 + (val / 255) * 44);
    }
    bars.value = newBars;

    animFrame = requestAnimationFrame(updateBars);
}

function cleanup() {
    if (animFrame) cancelAnimationFrame(animFrame);
    if (timerInterval) clearInterval(timerInterval);
    audioCtx?.close();
    animFrame = null;
    timerInterval = null;
    bars.value = new Array(24).fill(4);
}

function formatTime(s: number): string {
    const m = Math.floor(s / 60);
    const sec = s % 60;
    return `${m}:${sec.toString().padStart(2, '0')}`;
}

async function transcribeAudio(mimeType: string) {
    const ext = mimeType.includes('mp4') ? 'mp4' : 'webm';
    const blob = new Blob(audioChunks, { type: mimeType });
    const formData = new FormData();
    formData.append('audio', blob, `recording.${ext}`);

    try {
        const res = await fetch('/api/transcribe', {
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
            const err = await res.json().catch(() => ({}));
            throw new Error(err.message || 'Transcription failed');
        }
        const data = await res.json();
        transcript.value = data.text || '(Kein Text erkannt)';
    } catch (e) {
        transcript.value =
            '(Transkription fehlgeschlagen — bitte erneut versuchen)';
    }

    // Stream AI reaction
    phase.value = 'reacting';
    await aiReaction.stream('/api/transcribe/react', {
        text: transcript.value,
    });
    phase.value = 'done';
}

onUnmounted(() => {
    cleanup();
    mediaRecorder?.stream?.getTracks().forEach((t) => t.stop());
});
</script>

<template>
    <div class="flex min-h-0 flex-1 flex-col">
        <div class="flex-1 overflow-y-auto px-5 pb-4">
            <div
                class="rounded-2xl bg-white shadow-sm ring-1 ring-black/[0.04] dark:bg-[#1c1c1e] dark:ring-white/[0.06]"
            >
                <div class="p-6">
                    <div
                        class="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-[#ff9f0a]/10"
                    >
                        <AudioLines class="h-6 w-6 text-[#ff9f0a]" />
                    </div>

                    <h2
                        class="mb-2 text-[22px] leading-[1.25] font-bold tracking-[-0.4px] text-[#1d1d1f] dark:text-[#f5f5f7]"
                    >
                        Sprich mal rein.
                    </h2>

                    <p
                        class="mb-5 text-[15px] leading-[1.6] text-[#86868b] dark:text-[#98989d]"
                    >
                        Ein Mensch tippt ~40 Wörter pro Minute. Sprechend: ~150.
                        KI transkribiert 1 Stunde Audio in unter 10 Sekunden.
                        Probier's aus.
                    </p>

                    <!-- Intro: Mic button -->
                    <div
                        v-if="phase === 'intro'"
                        class="flex flex-col items-center gap-3 py-6"
                    >
                        <button
                            class="flex h-20 w-20 cursor-pointer items-center justify-center rounded-full bg-[#007aff] shadow-lg shadow-[#007aff]/25 transition-all active:scale-[0.93]"
                            @click="startRecording"
                        >
                            <Mic class="h-8 w-8 text-white" />
                        </button>
                        <span
                            class="text-[13px] text-[#86868b] dark:text-[#98989d]"
                            >Tippen zum Aufnehmen</span
                        >
                    </div>

                    <!-- Recording: Audio bars + Stop -->
                    <div v-else-if="phase === 'recording'" class="py-4">
                        <!-- Audio level bars -->
                        <div
                            class="mb-4 flex h-12 items-center justify-center gap-[3px]"
                        >
                            <div
                                v-for="(h, i) in bars"
                                :key="i"
                                class="w-[6px] rounded-full bg-[#007aff] transition-[height] duration-75"
                                :style="{ height: h + 'px' }"
                            />
                        </div>

                        <!-- Timer -->
                        <p
                            class="mb-4 text-center text-[28px] font-light tracking-wider text-[#1d1d1f] tabular-nums dark:text-[#f5f5f7]"
                        >
                            {{ formatTime(recordingTime) }}
                        </p>

                        <div class="flex flex-col items-center gap-3">
                            <button
                                class="flex h-16 w-16 cursor-pointer items-center justify-center rounded-full bg-[#ff3b30] shadow-lg shadow-[#ff3b30]/25 transition-all active:scale-[0.93]"
                                @click="stopRecording"
                            >
                                <Square class="h-5 w-5 fill-white text-white" />
                            </button>
                            <span
                                class="text-[13px] text-[#86868b] dark:text-[#98989d]"
                                >Tippen zum Stoppen</span
                            >
                        </div>
                    </div>

                    <!-- Transcribing spinner -->
                    <div
                        v-else-if="phase === 'transcribing'"
                        class="flex flex-col items-center gap-3 py-8"
                    >
                        <div
                            class="h-6 w-6 animate-spin rounded-full border-2 border-[#ff9f0a] border-t-transparent"
                        />
                        <span
                            class="text-[15px] text-[#86868b] dark:text-[#98989d]"
                            >Transkribiere via ElevenLabs...</span
                        >
                    </div>

                    <!-- Result: Transcript + AI Reaction -->
                    <div v-else class="space-y-4">
                        <div
                            class="rounded-xl bg-[#f5f5f7] p-4 dark:bg-[#2c2c2e]"
                        >
                            <p
                                class="mb-1 text-[11px] font-bold tracking-wider text-[#86868b] uppercase dark:text-[#98989d]"
                            >
                                Dein Text (Speech-to-Text)
                            </p>
                            <p
                                class="text-[15px] leading-[1.6] text-[#1d1d1f] dark:text-[#f5f5f7]"
                            >
                                {{ transcript }}
                            </p>
                        </div>

                        <div
                            v-if="
                                aiReaction.text.value ||
                                aiReaction.isLoading.value
                            "
                            class="flex items-start gap-2"
                        >
                            <Sparkles
                                class="mt-0.5 h-4 w-4 shrink-0 text-[#ff9f0a] opacity-60"
                            />
                            <p
                                class="text-[14px] leading-[1.5] text-[#86868b] italic dark:text-[#98989d]"
                            >
                                {{ aiReaction.text.value
                                }}<span
                                    v-if="aiReaction.isLoading.value"
                                    class="ml-0.5 inline-block h-[14px] w-[2px] animate-pulse bg-[#ff9f0a]"
                                />
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="shrink-0 px-5 pb-5">
            <button
                v-if="phase === 'done'"
                class="flex w-full cursor-pointer items-center justify-center gap-2 rounded-2xl bg-[#007aff] px-4 py-4 text-[17px] font-semibold tracking-[-0.2px] text-white transition-all active:scale-[0.97]"
                @click="$emit('next')"
            >
                Weiter
                <ChevronRight class="h-5 w-5" />
            </button>
        </div>
    </div>
</template>
