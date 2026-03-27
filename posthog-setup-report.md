# PostHog Analytics Setup Report

**Date:** 2026-03-27
**Project:** du-ai-challenge (Vue 3 + Inertia.js v3 + Laravel 13)
**PostHog Host:** EU Cloud (`eu.i.posthog.com`)
**Dashboard:** [Analytics basics](https://eu.posthog.com/project/148514/dashboard/590572)

---

## Events Instrumented

| Event | File | Properties |
|-------|------|------------|
| `journey_started` | `JourneyRenderer.vue` | — |
| `journey_restarted` | `JourneyRenderer.vue` | — |
| `persona_selected` | `JourneyRenderer.vue` | `persona` |
| `journey_completed` | `JourneyRenderer.vue` | `score`, `total`, `correct_count` |
| `ai_ready_cta_clicked` | `JourneyRenderer.vue` | — |
| `quiz_card_answered` | `QuizCard.vue` | `card_id`, `category`, `is_real`, `user_said_real`, `is_correct` |
| `choice_card_answered` | `ChoiceCard.vue` | `card_id`, `selected_index`, `correct_index`, `is_correct` |
| `ai_ready_checkout_initiated` | `AiReady.vue` | — |
| `ai_ready_checkout_completed` | `AiReady/Danke.vue` | — |

## Initialization

PostHog is initialized once in `resources/js/app.ts` using `VITE_POSTHOG_PROJECT_TOKEN` and `VITE_POSTHOG_HOST` environment variables. Frontend errors are automatically captured via `app.config.errorHandler`.

## Dashboard Insights

| Insight | Type | Link |
|---------|------|------|
| Journey → Checkout Funnel | Funnel (5 steps) | [72mHnUkA](https://eu.posthog.com/project/148514/insights/72mHnUkA) |
| Daily Active Users (Journey Start vs Completion) | Trends (line) | [ZWJYqN0X](https://eu.posthog.com/project/148514/insights/ZWJYqN0X) |
| Quiz Accuracy Rate (Correct vs Wrong Answers) | Trends (bar, breakdown) | [IVtuyKb4](https://eu.posthog.com/project/148514/insights/IVtuyKb4) |
| Persona Distribution | Trends (bar, breakdown by persona) | [Giml6wzW](https://eu.posthog.com/project/148514/insights/Giml6wzW) |
| AI Ready Conversion: CTA → Checkout | Trends (line, 3 series) | [uckM36AK](https://eu.posthog.com/project/148514/insights/uckM36AK) |

## Environment Variables Required

```
VITE_POSTHOG_PROJECT_TOKEN=<your token>
VITE_POSTHOG_HOST=https://eu.i.posthog.com
```
