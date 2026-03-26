<script setup lang="ts">
import { ref, onUnmounted } from 'vue';
import { Mic, MicOff, ChevronRight, Sparkles, AudioLines } from 'lucide-vue-next';
import { useStreamText } from '@/composables/useStreamText';

defineEmits<{ next: [] }>();

type Phase = 'intro' | 'recording' | 'transcribing' | 'reacting' | 'done';

const phase = ref<Phase>('intro');
const transcript = ref('');
const aiReaction = useStreamText();

let mediaRecorder: MediaRecorder | null = null;
let audioChunks: Blob[] = [];
let analyser: AnalyserNode | null = null;
let animFrame: number | null = null;
const canvasRef = ref<HTMLCanvasElement | null>(null);

function getXsrfToken(): string {
    return document.cookie
        .split('; ')
        .find((row) => row.startsWith('XSRF-TOKEN='))
        ?.split('=')[1]
        ?.replace(/%3D/g, '=') ?? '';
}

async function startRecording() {
    try {
        const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
        phase.value = 'recording';
        audioChunks = [];

        // Waveform visualization
        const audioCtx = new AudioContext();
        const source = audioCtx.createMediaStreamSource(stream);
        analyser = audioCtx.createAnalyser();
        analyser.fftSize = 256;
        source.connect(analyser);
        drawWaveform();

        mediaRecorder = new MediaRecorder(stream, { mimeType: 'audio/webm' });
        mediaRecorder.ondataavailable = (e) => {
            if (e.data.size > 0) audioChunks.push(e.data);
        };
        mediaRecorder.onstop = async () => {
            stream.getTracks().forEach((t) => t.stop());
            if (animFrame) cancelAnimationFrame(animFrame);
            audioCtx.close();

            phase.value = 'transcribing';
            await transcribeAudio();
        };

        mediaRecorder.start();
    } catch {
        // Mic permission denied — skip gracefully
        phase.value = 'done';
        transcript.value = '(Mikrofon nicht verfügbar)';
    }
}

function stopRecording() {
    mediaRecorder?.stop();
}

function drawWaveform() {
    const canvas = canvasRef.value;
    if (!canvas || !analyser) return;

    const ctx = canvas.getContext('2d');
    if (!ctx) return;

    const bufferLength = analyser.frequencyBinCount;
    const dataArray = new Uint8Array(bufferLength);

    function draw() {
        if (!analyser || !ctx || !canvas) return;
        animFrame = requestAnimationFrame(draw);

        analyser.getByteTimeDomainData(dataArray);

        ctx.fillStyle = 'rgba(0, 0, 0, 0)';
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        ctx.lineWidth = 2;
        ctx.strokeStyle = '#007aff';
        ctx.beginPath();

        const sliceWidth = canvas.width / bufferLength;
        let x = 0;

        for (let i = 0; i < bufferLength; i++) {
            const v = dataArray[i] / 128.0;
            const y = (v * canvas.height) / 2;
            if (i === 0) ctx.moveTo(x, y);
            else ctx.lineTo(x, y);
            x += sliceWidth;
        }

        ctx.lineTo(canvas.width, canvas.height / 2);
        ctx.stroke();
    }

    draw();
}

async function transcribeAudio() {
    const blob = new Blob(audioChunks, { type: 'audio/webm' });
    const formData = new FormData();
    formData.append('audio', blob, 'recording.webm');

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

        if (!res.ok) throw new Error('Transcription failed');
        const data = await res.json();
        transcript.value = data.text || '(Kein Text erkannt)';
    } catch {
        transcript.value = '(Transkription fehlgeschlagen)';
    }

    // Stream AI reaction
    phase.value = 'reacting';
    await aiReaction.stream('/api/transcribe/react', { text: transcript.value });
    phase.value = 'done';
}

onUnmounted(() => {
    if (animFrame) cancelAnimationFrame(animFrame);
    mediaRecorder?.stream?.getTracks().forEach((t) => t.stop());
});
</script>

<template>
    <div class="flex min-h-0 flex-1 flex-col">
        <div class="flex-1 overflow-y-auto px-5 pb-4">
            <div class="rounded-2xl bg-white shadow-sm ring-1 ring-black/[0.04] dark:bg-[#1c1c1e] dark:ring-white/[0.06]">
                <div class="p-6">
                    <div class="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-[#ff9f0a]/10">
                        <AudioLines class="h-6 w-6 text-[#ff9f0a]" />
                    </div>

                    <h2 class="mb-2 text-[22px] leading-[1.25] font-bold tracking-[-0.4px] text-[#1d1d1f] dark:text-[#f5f5f7]">
                        Sprich mal rein.
                    </h2>

                    <p class="mb-5 text-[15px] leading-[1.6] text-[#86868b] dark:text-[#98989d]">
                        Ein Mensch tippt ~40 Wörter pro Minute. Sprechend sind es ~150. KI transkribiert 1 Stunde Audio in unter 10 Sekunden. Probier's aus.
                    </p>

                    <!-- Intro: Mic button -->
                    <div v-if="phase === 'intro'" class="flex justify-center py-6">
                        <button
                            class="flex h-20 w-20 cursor-pointer items-center justify-center rounded-full bg-[#007aff] shadow-lg shadow-[#007aff]/25 transition-all active:scale-[0.93]"
                            @click="startRecording"
                        >
                            <Mic class="h-8 w-8 text-white" />
                        </button>
                    </div>

                    <!-- Recording: Waveform + Stop -->
                    <div v-else-if="phase === 'recording'" class="py-4">
                        <canvas
                            ref="canvasRef"
                            width="300"
                            height="60"
                            class="mx-auto mb-4 block w-full rounded-xl"
                        />
                        <div class="flex justify-center">
                            <button
                                class="flex h-16 w-16 cursor-pointer items-center justify-center rounded-full bg-[#ff3b30] shadow-lg shadow-[#ff3b30]/25 transition-all active:scale-[0.93]"
                                @click="stopRecording"
                            >
                                <MicOff class="h-6 w-6 text-white" />
                            </button>
                        </div>
                        <p class="mt-3 text-center text-[13px] text-[#86868b] dark:text-[#98989d]">
                            Aufnahme läuft... Tippe zum Stoppen.
                        </p>
                    </div>

                    <!-- Transcribing -->
                    <div v-else-if="phase === 'transcribing'" class="flex items-center justify-center gap-3 py-8">
                        <div class="h-5 w-5 animate-spin rounded-full border-2 border-[#007aff] border-t-transparent" />
                        <span class="text-[15px] text-[#86868b] dark:text-[#98989d]">Transkribiere...</span>
                    </div>

                    <!-- Transcript + AI Reaction -->
                    <div v-else class="space-y-4">
                        <!-- Transcript -->
                        <div class="rounded-xl bg-[#f5f5f7] p-4 dark:bg-[#2c2c2e]">
                            <p class="mb-1 text-[11px] font-bold tracking-wider text-[#86868b] uppercase dark:text-[#98989d]">
                                Dein Text (Speech-to-Text)
                            </p>
                            <p class="text-[15px] leading-[1.6] text-[#1d1d1f] dark:text-[#f5f5f7]">
                                {{ transcript }}
                            </p>
                        </div>

                        <!-- AI Reaction (streamed) -->
                        <div v-if="aiReaction.text.value || aiReaction.isLoading.value" class="flex items-start gap-2">
                            <Sparkles class="mt-0.5 h-4 w-4 shrink-0 text-[#ff9f0a] opacity-60" />
                            <p class="text-[14px] leading-[1.5] text-[#86868b] italic dark:text-[#98989d]">
                                {{ aiReaction.text.value }}<span
                                    v-if="aiReaction.isLoading.value"
                                    class="ml-0.5 inline-block h-[14px] w-[2px] animate-pulse bg-[#ff9f0a]"
                                />
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action -->
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
