<?php

namespace App\Http\Controllers;

use App\Models\JourneySession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class JourneySessionController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'learning_journey_id' => ['required', 'exists:learning_journeys,id'],
        ]);

        Log::info('JourneySessionController@store', ['learning_journey_id' => $validated['learning_journey_id']]);

        $session = JourneySession::create([
            'learning_journey_id' => $validated['learning_journey_id'],
        ]);

        return response()->json([
            'session_id' => $session->nanoid,
        ]);
    }

    public function show(JourneySession $journeySession): JsonResponse
    {
        Log::info('JourneySessionController@show', ['session_id' => $journeySession->nanoid]);

        return response()->json([
            'session_id' => $journeySession->nanoid,
            'current_block' => $journeySession->current_block,
            'current_item' => $journeySession->current_item,
            'answers' => $journeySession->answers ?? [],
            'persona_style' => $journeySession->persona_style,
            'persona_prompt_shown' => $journeySession->persona_prompt_shown,
            'completed' => $journeySession->completed_at !== null,
        ]);
    }

    public function update(Request $request, JourneySession $journeySession): JsonResponse
    {
        $validated = $request->validate([
            'current_block' => ['sometimes', 'integer', 'min:0'],
            'current_item' => ['sometimes', 'integer', 'min:0'],
            'answers' => ['sometimes', 'array'],
        ]);

        Log::info('JourneySessionController@update', ['session_id' => $journeySession->nanoid, 'fields' => array_keys($validated)]);

        $journeySession->update($validated);

        return response()->json(['ok' => true]);
    }

    public function setPersona(Request $request, JourneySession $journeySession): JsonResponse
    {
        $validated = $request->validate([
            'persona_style' => ['required', 'string', 'max:200'],
        ]);

        Log::info('JourneySessionController@setPersona', ['session_id' => $journeySession->nanoid]);

        $journeySession->update([
            'persona_style' => $validated['persona_style'],
            'persona_prompt_shown' => true,
        ]);

        return response()->json(['ok' => true]);
    }
}
