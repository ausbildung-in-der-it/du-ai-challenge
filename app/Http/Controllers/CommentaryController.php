<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateCommentaryJob;
use App\Models\Commentary;
use App\Models\JourneySession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommentaryController extends Controller
{
    public function store(Request $request, JourneySession $journeySession): JsonResponse
    {
        $validated = $request->validate([
            'quiz_card_id' => ['required', 'integer', 'exists:quiz_cards,id'],
            'persona_style' => ['nullable', 'string', 'max:200'],
        ]);

        $commentary = Commentary::create([
            'journey_session_id' => $journeySession->id,
            'quiz_card_id' => $validated['quiz_card_id'],
            'persona_style' => $validated['persona_style'] ?? $journeySession->persona_style,
            'status' => 'pending',
        ]);

        GenerateCommentaryJob::dispatch($commentary->id);

        return response()->json([
            'commentary_id' => $commentary->nanoid,
        ]);
    }

    public function show(JourneySession $journeySession, Commentary $commentary): JsonResponse
    {
        return response()->json([
            'status' => $commentary->status,
            'text' => $commentary->text,
            'prompt_used' => $commentary->prompt_used,
        ]);
    }
}
