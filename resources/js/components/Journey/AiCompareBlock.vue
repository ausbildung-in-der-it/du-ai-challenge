<script setup lang="ts">
import { ref, onMounted } from 'vue';
import {
    Brain,
    Globe,
    ChevronRight,
    Zap,
    Sparkles,
    Search,
} from 'lucide-vue-next';

type EvalResult = {
    provider: string;
    model: string;
    tier: 'fast' | 'smart';
    response: string;
    status: 'done' | 'error';
};

const props = defineProps<{
    quizCardId: number;
    headline: string;
    isReal: boolean;
    webSearch?: boolean;
}>();

defineEmits<{
    next: [];
}>();

const results = ref<EvalResult[]>([]);
const loading = ref(true);
const allDone = ref(false);
const canSkip = ref(false);

function getXsrfToken(): string {
    return (
        document.cookie
            .split('; ')
            .find((row) => row.startsWith('XSRF-TOKEN='))
            ?.split('=')[1]
            ?.replace(/%3D/g, '=') ?? ''
    );
}

onMounted(async () => {
    // Failsafe: allow skip after 15 seconds regardless
    const skipTimer = setTimeout(() => {
        canSkip.value = true;
    }, 15000);

    try {
        const res = await fetch('/api/ai-compare/stream', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'text/event-stream',
                'X-XSRF-TOKEN': getXsrfToken(),
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({
                quiz_card_id: props.quizCardId,
                web_search: props.webSearch ?? false,
            }),
        });

        if (!res.ok || !res.body) {
            loading.value = false;
            return;
        }

        const reader = res.body.getReader();
        const decoder = new TextDecoder();
        let buffer = '';

        while (true) {
            const { done, value } = await reader.read();
            if (done) break;

            buffer += decoder.decode(value, { stream: true });
            const lines = buffer.split('\n');
            buffer = lines.pop() ?? '';

            for (const line of lines) {
                const trimmed = line.trim();
                if (!trimmed.startsWith('data: ')) continue;
                const payload = trimmed.slice(6);
                if (payload === '[DONE]') continue;

                try {
                    const result: EvalResult = JSON.parse(payload);
                    results.value.push(result);
                } catch {
                    /* skip */
                }
            }
        }
    } catch {
        /* network error */
    }

    loading.value = false;
    allDone.value = true;
    clearTimeout(skipTimer);
});

function parseResponse(response: string): {
    verdict: string;
    confidence: string;
    reason: string;
} {
    const parts = response.split('|').map((s) => s.trim());
    return {
        verdict: parts[0]?.toLowerCase().includes('echt') ? 'echt' : 'erfunden',
        confidence: parts[1]?.replace('Konfidenz:', '').trim() ?? '?/10',
        reason: parts[2]?.replace('Begründung:', '').trim() ?? response,
    };
}

function isCorrect(verdict: string): boolean {
    return (verdict === 'echt') === props.isReal;
}

const providerColors: Record<string, string> = {
    Anthropic: '#d97706',
    OpenAI: '#10b981',
    Google: '#3b82f6',
};
</script>

<template>
    <div class="flex min-h-0 flex-1 flex-col">
        <div class="flex-1 overflow-y-auto px-5 pb-4">
            <div
                class="rounded-2xl bg-white shadow-sm ring-1 ring-black/[0.04] dark:bg-[#1c1c1e] dark:ring-white/[0.06]"
            >
                <div class="p-6">
                    <div
                        class="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-2xl"
                        :class="
                            webSearch ? 'bg-[#34c759]/10' : 'bg-purple-500/10'
                        "
                    >
                        <Globe
                            v-if="webSearch"
                            class="h-6 w-6 text-[#34c759]"
                        />
                        <Brain v-else class="h-6 w-6 text-purple-500" />
                    </div>

                    <!-- Mode badge -->
                    <div
                        class="mb-3 inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1 text-[11px] font-bold tracking-wider uppercase"
                        :class="
                            webSearch
                                ? 'bg-[#34c759]/10 text-[#34c759]'
                                : 'bg-[#ff9f0a]/10 text-[#ff9f0a]'
                        "
                    >
                        <Search v-if="webSearch" class="h-3 w-3" />
                        {{ webSearch ? 'Mit Web-Suche' : 'Ohne Web-Suche' }}
                    </div>

                    <h2
                        class="mb-2 text-[22px] leading-[1.25] font-bold tracking-[-0.4px] text-[#1d1d1f] dark:text-[#f5f5f7]"
                    >
                        {{
                            webSearch
                                ? 'Und jetzt: dieselben KIs mit Internet-Zugang'
                                : 'Wie bewerten verschiedene KIs diese Story?'
                        }}
                    </h2>

                    <p
                        class="mb-1 text-[15px] leading-[1.6] text-[#86868b] dark:text-[#98989d]"
                    >
                        {{
                            webSearch
                                ? 'Dieselben Modelle, aber jetzt dürfen sie im Web recherchieren. Ändert sich was?'
                                : 'Nur Trainingswissen, kein Internet. Wer liegt richtig?'
                        }}
                    </p>

                    <p
                        class="mb-5 text-[13px] font-medium text-[#1d1d1f] dark:text-[#f5f5f7]"
                    >
                        "{{ headline }}"
                    </p>

                    <!-- Results Grid -->
                    <div class="space-y-3">
                        <TransitionGroup
                            enter-active-class="transition-all duration-400 ease-out"
                            enter-from-class="opacity-0 translate-y-2 scale-[0.98]"
                            enter-to-class="opacity-100 translate-y-0 scale-100"
                        >
                            <div
                                v-for="(result, i) in results"
                                :key="i"
                                class="rounded-xl p-3.5"
                                :class="
                                    result.status === 'error'
                                        ? 'bg-[#f5f5f7] dark:bg-[#2c2c2e]'
                                        : 'bg-[#f5f5f7] dark:bg-[#2c2c2e]'
                                "
                            >
                                <div
                                    class="mb-2 flex items-center justify-between"
                                >
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="h-2 w-2 rounded-full"
                                            :style="{
                                                backgroundColor:
                                                    providerColors[
                                                        result.provider
                                                    ] ?? '#86868b',
                                            }"
                                        />
                                        <span
                                            class="text-[13px] font-semibold text-[#1d1d1f] dark:text-[#f5f5f7]"
                                        >
                                            {{ result.provider }}
                                        </span>
                                        <span
                                            class="text-[11px] text-[#86868b] dark:text-[#98989d]"
                                        >
                                            {{ result.model }}
                                        </span>
                                        <span
                                            class="rounded px-1.5 py-0.5 text-[10px] font-bold uppercase"
                                            :class="
                                                result.tier === 'smart'
                                                    ? 'bg-purple-500/10 text-purple-500'
                                                    : 'bg-[#007aff]/10 text-[#007aff]'
                                            "
                                        >
                                            <Sparkles
                                                v-if="result.tier === 'smart'"
                                                class="mr-0.5 inline h-2.5 w-2.5"
                                            />
                                            <Zap
                                                v-else
                                                class="mr-0.5 inline h-2.5 w-2.5"
                                            />
                                            {{
                                                result.tier === 'smart'
                                                    ? 'smart'
                                                    : 'fast'
                                            }}
                                        </span>
                                    </div>
                                </div>

                                <template v-if="result.status === 'done'">
                                    <div class="flex items-start gap-2">
                                        <span
                                            class="mt-0.5 shrink-0 text-[13px] font-bold"
                                            :class="
                                                isCorrect(
                                                    parseResponse(
                                                        result.response,
                                                    ).verdict,
                                                )
                                                    ? 'text-[#34c759]'
                                                    : 'text-[#ff3b30]'
                                            "
                                        >
                                            {{
                                                isCorrect(
                                                    parseResponse(
                                                        result.response,
                                                    ).verdict,
                                                )
                                                    ? '✓'
                                                    : '✕'
                                            }}
                                        </span>
                                        <p
                                            class="text-[13px] leading-[1.5] text-[#1d1d1f] dark:text-[#f5f5f7]"
                                        >
                                            {{ result.response }}
                                        </p>
                                    </div>
                                </template>
                                <p
                                    v-else
                                    class="text-[13px] text-[#86868b] italic dark:text-[#98989d]"
                                >
                                    Nicht verfügbar
                                </p>
                            </div>
                        </TransitionGroup>

                        <!-- Loading skeleton -->
                        <div
                            v-if="loading"
                            class="flex items-center gap-2 rounded-xl bg-[#f5f5f7] p-3.5 dark:bg-[#2c2c2e]"
                        >
                            <div
                                class="h-4 w-4 animate-spin rounded-full border-2 border-purple-500 border-t-transparent"
                            />
                            <span
                                class="text-[13px] text-[#86868b] dark:text-[#98989d]"
                            >
                                Nächstes Modell evaluiert...
                            </span>
                        </div>
                    </div>

                    <!-- Summary after all done -->
                    <div
                        v-if="allDone && results.length > 0"
                        class="mt-4 rounded-xl p-3.5"
                        :class="
                            webSearch
                                ? 'bg-[#34c759]/5 dark:bg-[#34c759]/10'
                                : 'bg-[#007aff]/5 dark:bg-[#007aff]/10'
                        "
                    >
                        <p
                            class="text-[13px] leading-[1.5] text-[#1d1d1f] dark:text-[#f5f5f7]"
                        >
                            <strong>{{
                                results.filter(
                                    (r) =>
                                        r.status === 'done' &&
                                        isCorrect(
                                            parseResponse(r.response).verdict,
                                        ),
                                ).length
                            }}</strong>
                            von
                            <strong>{{
                                results.filter((r) => r.status === 'done')
                                    .length
                            }}</strong>
                            Modellen lagen richtig.
                            <template v-if="webSearch">
                                Mit Web-Zugang steigt die Konfidenz — KI-Agenten
                                sind stärker, wenn sie Werkzeuge haben. Genau
                                wie Menschen.
                            </template>
                            <template v-else>
                                Ohne Internet raten selbst die besten Modelle.
                                Im nächsten Schritt geben wir ihnen Web-Zugang.
                            </template>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="shrink-0 px-5 pb-5">
            <button
                class="flex w-full cursor-pointer items-center justify-center gap-2 rounded-2xl bg-[#007aff] px-4 py-4 text-[17px] font-semibold tracking-[-0.2px] text-white transition-all active:scale-[0.97] disabled:opacity-40"
                :disabled="!allDone && !canSkip"
                @click="$emit('next')"
            >
                Weiter
                <ChevronRight class="h-5 w-5" />
            </button>
        </div>
    </div>
</template>
