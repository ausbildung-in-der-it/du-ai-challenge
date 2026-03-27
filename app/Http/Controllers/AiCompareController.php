<?php

namespace App\Http\Controllers;

use App\Agents\StoryEvaluatorAgent;
use App\Agents\StoryEvaluatorWithSearchAgent;
use App\Models\QuizCard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Laravel\Ai\Enums\Lab;
use Throwable;

class AiCompareController extends Controller
{
    /**
     * Evaluate a story across multiple AI providers.
     * Returns results as they complete (single JSON response after all done).
     */
    public function evaluate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'quiz_card_id' => ['required', 'integer', 'exists:quiz_cards,id'],
        ]);

        Log::info('AiCompareController@evaluate', ['quiz_card_id' => $validated['quiz_card_id']]);

        $card = QuizCard::findOrFail($validated['quiz_card_id']);
        $story = $card->headline.'. '.$card->story;

        $providers = $this->getAvailableProviders();
        $results = [];

        foreach ($providers as $config) {
            $agent = new StoryEvaluatorAgent(story: $story);

            try {
                $response = $agent->prompt(
                    'Bewerte diese Story.',
                    provider: $config['provider'],
                    model: $config['model'] ?? null,
                    timeout: 15,
                );

                $results[] = [
                    'provider' => $config['label'],
                    'model' => $config['model_label'],
                    'tier' => $config['tier'],
                    'response' => trim((string) $response),
                    'status' => 'done',
                ];
            } catch (Throwable $e) {
                Log::error('AiCompareController@evaluate: provider failed', ['provider' => $config['label'], 'model' => $config['model_label'], 'error' => $e->getMessage()]);

                $results[] = [
                    'provider' => $config['label'],
                    'model' => $config['model_label'],
                    'tier' => $config['tier'],
                    'response' => 'Nicht verfügbar',
                    'status' => 'error',
                    'error' => $e->getMessage(),
                ];
            }
        }

        return response()->json([
            'card_headline' => $card->headline,
            'is_real' => $card->is_real,
            'results' => $results,
        ]);
    }

    /**
     * Stream evaluations one by one as SSE.
     */
    public function stream(Request $request)
    {
        $validated = $request->validate([
            'quiz_card_id' => ['required', 'integer', 'exists:quiz_cards,id'],
            'web_search' => ['sometimes', 'boolean'],
        ]);

        $webSearch = filter_var($validated['web_search'] ?? false, FILTER_VALIDATE_BOOLEAN);

        Log::info('AiCompareController@stream', [
            'quiz_card_id' => $validated['quiz_card_id'],
            'web_search' => $webSearch,
        ]);

        $card = QuizCard::findOrFail($validated['quiz_card_id']);
        $story = $card->headline.'. '.$card->story;
        $providers = $this->getAvailableProviders();

        return response()->stream(function () use ($story, $providers, $webSearch) {
            foreach ($providers as $config) {
                $agent = $webSearch
                    ? new StoryEvaluatorWithSearchAgent(story: $story)
                    : new StoryEvaluatorAgent(story: $story);

                try {
                    $response = $agent->prompt(
                        'Bewerte diese Story.',
                        provider: $config['provider'],
                        model: $config['model'] ?? null,
                        timeout: 15,
                    );

                    $result = [
                        'provider' => $config['label'],
                        'model' => $config['model_label'],
                        'tier' => $config['tier'],
                        'response' => trim((string) $response),
                        'status' => 'done',
                    ];
                } catch (Throwable $e) {
                    Log::error('AiCompareController@stream: provider failed', ['provider' => $config['label'], 'model' => $config['model_label'], 'error' => $e->getMessage()]);

                    $result = [
                        'provider' => $config['label'],
                        'model' => $config['model_label'],
                        'tier' => $config['tier'],
                        'response' => 'Nicht verfügbar',
                        'status' => 'error',
                    ];
                }

                echo 'data: '.json_encode($result)."\n\n";

                if (ob_get_level()) {
                    ob_flush();
                }
                flush();
            }

            echo "data: [DONE]\n\n";

            if (ob_get_level()) {
                ob_flush();
            }
            flush();
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
        ]);
    }

    private function getAvailableProviders(): array
    {
        $providers = [];

        if (config('ai.providers.anthropic.key')) {
            $providers[] = [
                'provider' => Lab::Anthropic,
                'model' => 'claude-haiku-4-5-20251001',
                'model_label' => 'Haiku 4.5',
                'label' => 'Anthropic',
                'tier' => 'fast',
            ];
            $providers[] = [
                'provider' => Lab::Anthropic,
                'model' => 'claude-sonnet-4-6',
                'model_label' => 'Sonnet 4.6',
                'label' => 'Anthropic',
                'tier' => 'smart',
            ];
        }

        if (config('ai.providers.openai.key')) {
            $providers[] = [
                'provider' => Lab::OpenAI,
                'model' => 'gpt-4.1-mini',
                'model_label' => 'GPT-4.1 Mini',
                'label' => 'OpenAI',
                'tier' => 'fast',
            ];
            $providers[] = [
                'provider' => Lab::OpenAI,
                'model' => 'gpt-4.1',
                'model_label' => 'GPT-4.1',
                'label' => 'OpenAI',
                'tier' => 'smart',
            ];
        }

        if (config('ai.providers.gemini.key')) {
            $providers[] = [
                'provider' => Lab::Gemini,
                'model' => 'gemini-2.0-flash',
                'model_label' => 'Flash 2.0',
                'label' => 'Google',
                'tier' => 'fast',
            ];
            $providers[] = [
                'provider' => Lab::Gemini,
                'model' => 'gemini-2.5-pro',
                'model_label' => 'Pro 2.5',
                'label' => 'Google',
                'tier' => 'smart',
            ];
        }

        return $providers;
    }
}
