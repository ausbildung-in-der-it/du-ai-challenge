<script setup lang="ts">
import { computed, reactive, ref, watch } from 'vue';
import { CheckCircle2, LoaderCircle } from 'lucide-vue-next';
import { store as storeNewsletterLead } from '@/actions/App/Http/Controllers/NewsletterLeadController';

type LeadSource = 'homepage' | 'challenge_complete';

const props = withDefaults(
    defineProps<{
        source: LeadSource;
        journeySlug?: string | null;
        journeySessionNanoid?: string | null;
        completedChallenge?: boolean;
        submitLabel?: string;
        successTitle?: string;
        successMessage?: string;
    }>(),
    {
        journeySlug: null,
        journeySessionNanoid: null,
        completedChallenge: false,
        submitLabel: 'Eintragen',
        successTitle: 'Eintrag gespeichert',
        successMessage: 'Wir haben deine Daten gespeichert.',
    },
);

const form = reactive({
    email: '',
    linkedin_url: '',
    privacy_accepted: false,
});

const errors = reactive<Record<string, string>>({});
const processing = ref(false);
const submitted = ref(false);
const globalError = ref<string | null>(null);

const buttonLabel = computed(() =>
    processing.value ? 'Wird gespeichert ...' : props.submitLabel,
);

function getXsrfToken(): string {
    return (
        document.cookie
            .split('; ')
            .find((row) => row.startsWith('XSRF-TOKEN='))
            ?.split('=')[1]
            ?.replace(/%3D/g, '=') ?? ''
    );
}

function clearErrors() {
    Object.keys(errors).forEach((key) => {
        delete errors[key];
    });
    globalError.value = null;
}

watch(
    () => [form.email, form.linkedin_url, form.privacy_accepted],
    () => clearErrors(),
);

async function submit() {
    if (processing.value || submitted.value) {
        return;
    }

    processing.value = true;
    clearErrors();

    try {
        const response = await fetch(storeNewsletterLead.url(), {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-XSRF-TOKEN': getXsrfToken(),
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({
                email: form.email,
                linkedin_url: form.linkedin_url || null,
                privacy_accepted: form.privacy_accepted,
                source: props.source,
                journey_slug: props.journeySlug,
                journey_session_nanoid: props.journeySessionNanoid,
                completed_challenge: props.completedChallenge,
            }),
        });

        if (response.status === 422) {
            const payload = await response.json();
            const rawErrors = payload.errors ?? {};
            const flatErrors: Record<string, string> = {};
            for (const [key, value] of Object.entries(rawErrors)) {
                flatErrors[key] = Array.isArray(value) ? value[0] : (value as string);
            }
            Object.assign(errors, flatErrors);
            return;
        }

        if (!response.ok) {
            throw new Error(`Unexpected response status ${response.status}`);
        }

        submitted.value = true;
    } catch {
        globalError.value =
            'Speichern fehlgeschlagen. Bitte versuche es gleich noch einmal.';
    } finally {
        processing.value = false;
    }
}
</script>

<template>
    <div class="space-y-4">
        <div
            v-if="submitted"
            class="rounded-2xl border border-[#34c759]/20 bg-[#34c759]/8 p-4 text-left"
        >
            <div class="flex items-start gap-3">
                <CheckCircle2 class="mt-0.5 h-5 w-5 shrink-0 text-[#34c759]" />
                <div>
                    <p
                        class="text-[15px] font-semibold text-[#1d1d1f] dark:text-[#f5f5f7]"
                    >
                        {{ successTitle }}
                    </p>
                    <p
                        class="mt-1 text-[14px] leading-[1.55] text-[#3b3b3f] dark:text-white/65"
                    >
                        {{ successMessage }}
                    </p>
                </div>
            </div>
        </div>

        <form v-else class="space-y-4" @submit.prevent="submit">
            <div class="space-y-2">
                <label
                    for="newsletter-email"
                    class="block text-[13px] font-semibold tracking-[0.08em] text-[#6e6e73] uppercase dark:text-white/40"
                >
                    E-Mail-Adresse
                </label>
                <input
                    id="newsletter-email"
                    v-model="form.email"
                    type="email"
                    autocomplete="email"
                    placeholder="name@firma.de"
                    class="w-full rounded-2xl border border-black/10 bg-white px-4 py-3.5 text-[16px] text-[#1d1d1f] transition outline-none placeholder:text-[#86868b] focus:border-[#007aff] focus:ring-4 focus:ring-[#007aff]/12 dark:border-white/10 dark:bg-white/5 dark:text-[#f5f5f7] dark:placeholder:text-white/25"
                />
                <p v-if="errors.email" class="text-[13px] text-[#ff453a]">
                    {{ errors.email }}
                </p>
            </div>

            <div class="space-y-2">
                <label
                    for="newsletter-linkedin"
                    class="block text-[13px] font-semibold tracking-[0.08em] text-[#6e6e73] uppercase dark:text-white/40"
                >
                    LinkedIn
                    <span
                        class="ml-1 tracking-normal text-[#86868b] normal-case dark:text-white/25"
                    >
                        optional
                    </span>
                </label>
                <input
                    id="newsletter-linkedin"
                    v-model="form.linkedin_url"
                    type="text"
                    autocomplete="url"
                    placeholder="https://www.linkedin.com/in/..."
                    class="w-full rounded-2xl border border-black/10 bg-white px-4 py-3.5 text-[16px] text-[#1d1d1f] transition outline-none placeholder:text-[#86868b] focus:border-[#007aff] focus:ring-4 focus:ring-[#007aff]/12 dark:border-white/10 dark:bg-white/5 dark:text-[#f5f5f7] dark:placeholder:text-white/25"
                />
                <p
                    class="text-[13px] leading-[1.45] text-[#86868b] dark:text-white/30"
                >
                    Hilft uns bei der Zuordnung, ist aber nicht erforderlich.
                </p>
                <p
                    v-if="errors.linkedin_url"
                    class="text-[13px] text-[#ff453a]"
                >
                    {{ errors.linkedin_url }}
                </p>
            </div>

            <div class="space-y-2">
                <label
                    class="flex items-start gap-3 rounded-2xl bg-black/[0.03] p-4 dark:bg-white/[0.04]"
                >
                    <input
                        v-model="form.privacy_accepted"
                        type="checkbox"
                        class="mt-1 h-4 w-4 shrink-0 cursor-pointer rounded accent-[#007aff]"
                    />
                    <span
                        class="text-[14px] leading-[1.6] text-[#4b4b52] dark:text-white/60"
                    >
                        Ich akzeptiere die
                        <a
                            href="https://ausbildung-in-der-it.de/datenschutz"
                            target="_blank"
                            rel="noopener"
                            class="font-medium text-[#007aff] underline decoration-[#007aff]/25 underline-offset-3"
                        >
                            Datenschutzerklärung
                        </a>
                        und bin mit der Kontaktaufnahme zum Versand des Double
                        Opt-ins einverstanden.
                    </span>
                </label>
                <p
                    v-if="errors.privacy_accepted"
                    class="text-[13px] text-[#ff453a]"
                >
                    {{ errors.privacy_accepted }}
                </p>
            </div>

            <p v-if="globalError" class="text-[13px] text-[#ff453a]">
                {{ globalError }}
            </p>

            <button
                type="submit"
                :disabled="processing"
                class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-[#007aff] px-5 py-3.5 text-[16px] font-semibold text-white shadow-lg shadow-[#007aff]/20 transition active:scale-[0.98] disabled:cursor-not-allowed disabled:opacity-70"
            >
                <LoaderCircle v-if="processing" class="h-4 w-4 animate-spin" />
                {{ buttonLabel }}
            </button>
        </form>
    </div>
</template>
