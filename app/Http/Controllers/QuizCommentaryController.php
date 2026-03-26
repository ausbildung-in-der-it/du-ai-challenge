<?php

namespace App\Http\Controllers;

use App\Agents\QuizCommentaryAgent;
use App\Models\QuizCard;
use Illuminate\Http\Request;
use Laravel\Ai\Responses\StreamableAgentResponse;

class QuizCommentaryController extends Controller
{
    public function __invoke(Request $request): StreamableAgentResponse
    {
        $validated = $request->validate([
            'quiz_card_id' => ['required', 'integer', 'exists:quiz_cards,id'],
            'user_said_real' => ['required'],
        ]);

        $card = QuizCard::findOrFail($validated['quiz_card_id']);

        $agent = new QuizCommentaryAgent(
            headline: $card->headline,
            isReal: $card->is_real,
            userSaidReal: filter_var($validated['user_said_real'], FILTER_VALIDATE_BOOLEAN),
        );

        return $agent
            ->stream('Kommentiere diese Quiz-Antwort.')
            ->usingVercelDataProtocol();
    }
}
