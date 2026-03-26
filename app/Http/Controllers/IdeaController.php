<?php

namespace App\Http\Controllers;

use App\Agents\IdeaGeneratorAgent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Ai\Responses\StreamableAgentResponse;
use Laravel\Ai\Transcription;

class IdeaController extends Controller
{
    /**
     * Transcribe an audio file to text.
     */
    public function transcribe(Request $request): JsonResponse
    {
        $request->validate([
            'audio' => ['required', 'file', 'max:10240'],
        ]);

        $transcript = Transcription::fromUpload($request->file('audio'))->generate();

        return response()->json(['text' => (string) $transcript]);
    }

    /**
     * Stream AI-generated ideas based on a business description.
     */
    public function generate(Request $request): StreamableAgentResponse
    {
        $validated = $request->validate([
            'prompt' => ['required', 'string', 'min:10', 'max:2000'],
        ]);

        $agent = new IdeaGeneratorAgent;

        return $agent
            ->stream($validated['prompt'])
            ->usingVercelDataProtocol();
    }
}
