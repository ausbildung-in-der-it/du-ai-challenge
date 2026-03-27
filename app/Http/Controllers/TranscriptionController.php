<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Laravel\Ai\Enums\Lab;
use Laravel\Ai\Responses\StreamableAgentResponse;
use Laravel\Ai\Transcription;

use function Laravel\Ai\agent;

class TranscriptionController extends Controller
{
    public function transcribe(Request $request): JsonResponse
    {
        $request->validate([
            'audio' => ['required', 'file', 'max:10240'],
        ]);

        Log::info('TranscriptionController@transcribe', ['file_size' => $request->file('audio')?->getSize()]);

        $transcript = Transcription::fromUpload($request->file('audio'))
            ->generate(provider: Lab::ElevenLabs);

        return response()->json(['text' => (string) $transcript]);
    }

    public function react(Request $request): StreamableAgentResponse
    {
        $request->validate([
            'text' => ['required', 'string', 'max:2000'],
        ]);

        $text = $request->input('text');
        $wordCount = str_word_count($text);

        Log::info('TranscriptionController@react', ['word_count' => $wordCount]);

        return agent(
            instructions: <<<PROMPT
            Du bist ein freundlicher, empathischer KI-Begleiter auf dem data:unplugged Festival 2026.

            Der User hat gerade Speech-to-Text ausprobiert und folgenden Text eingesprochen:
            "{$text}"

            Das waren ca. {$wordCount} Wörter.

            Reagiere darauf:
            1. Bestätige kurz und sympathisch was er/sie gesagt hat
            2. Erkläre in 1 Satz was technisch passiert ist (Audio wurde an ElevenLabs gesendet und per KI transkribiert)
            3. Nenne einen Fakt: Ein Mensch tippt ca. 40 Wörter pro Minute. Sprechend sind es ca. 150. KI-Transkription verarbeitet 1 Stunde Audio in unter 10 Sekunden.
            4. Schließe mit einem ermutigenden Satz

            Maximal 4 kurze Sätze. Kein Emoji. Deutsch.
            PROMPT,
            messages: [],
            tools: [],
        )->stream(
            'Reagiere auf die Transkription.',
            provider: Lab::Anthropic,
            model: 'claude-haiku-4-5-20251001',
        )->usingVercelDataProtocol();
    }
}
