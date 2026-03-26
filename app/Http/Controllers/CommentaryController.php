<?php

namespace App\Http\Controllers;

use App\Agents\QuizCommentaryAgent;
use App\Models\Commentary;
use App\Models\JourneySession;
use App\Models\QuizCard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Ai\Responses\StreamableAgentResponse;
use Laravel\Ai\Responses\StreamedAgentResponse;

class CommentaryController extends Controller
{
    /**
     * Stream a commentary via SSE (sync, low latency).
     * Saves result to DB in `then` callback for later retrieval.
     */
    public function store(Request $request, JourneySession $journeySession): StreamableAgentResponse
    {
        $validated = $request->validate([
            'quiz_card_id' => ['required', 'integer', 'exists:quiz_cards,id'],
            'persona_style' => ['nullable', 'string', 'max:200'],
        ]);

        $card = QuizCard::findOrFail($validated['quiz_card_id']);
        $personaStyle = $validated['persona_style'] ?? $journeySession->persona_style;

        $answers = $journeySession->answers ?? [];
        $answer = $answers[(string) $card->id] ?? null;
        $userSaidReal = $answer['user_said_real'] ?? true;

        $agent = new QuizCommentaryAgent(
            headline: $card->headline,
            isReal: $card->is_real,
            userSaidReal: $userSaidReal,
            personaStyle: $personaStyle,
        );

        $promptUsed = $agent->instructions();

        // Create commentary record (will be updated after stream)
        $commentary = Commentary::create([
            'journey_session_id' => $journeySession->id,
            'quiz_card_id' => $card->id,
            'persona_style' => $personaStyle,
            'status' => 'processing',
            'prompt_used' => $promptUsed,
        ]);

        return $agent
            ->stream('Kommentiere diese Quiz-Antwort.')
            ->usingVercelDataProtocol()
            ->then(function (StreamedAgentResponse $response) use ($commentary) {
                $commentary->update([
                    'status' => 'done',
                    'text' => $response->text,
                ]);
            });
    }

    /**
     * Get a saved commentary (for prompt reveal).
     */
    public function show(JourneySession $journeySession, Commentary $commentary): JsonResponse
    {
        return response()->json([
            'status' => $commentary->status,
            'text' => $commentary->text,
            'prompt_used' => $commentary->prompt_used,
        ]);
    }

    /**
     * Get the latest commentary for a quiz card in this session.
     */
    public function latest(JourneySession $journeySession, QuizCard $quizCard): JsonResponse
    {
        $commentary = Commentary::where('journey_session_id', $journeySession->id)
            ->where('quiz_card_id', $quizCard->id)
            ->latest()
            ->first();

        if (! $commentary) {
            return response()->json(['status' => 'not_found'], 404);
        }

        return response()->json([
            'commentary_id' => $commentary->nanoid,
            'status' => $commentary->status,
            'text' => $commentary->text,
            'prompt_used' => $commentary->prompt_used,
        ]);
    }
}
