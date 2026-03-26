<?php

namespace App\Jobs;

use App\Agents\QuizCommentaryAgent;
use App\Models\Commentary;
use App\Models\QuizCard;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

class GenerateCommentaryJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 2;

    public int $timeout = 30;

    public function __construct(
        public readonly int $commentaryId,
    ) {}

    public function handle(): void
    {
        $commentary = Commentary::findOrFail($this->commentaryId);
        $card = QuizCard::findOrFail($commentary->quiz_card_id);

        $commentary->update(['status' => 'processing']);

        $answers = $commentary->session->answers ?? [];
        $answer = $answers[$card->id] ?? null;
        $userSaidReal = $answer['user_said_real'] ?? true;

        $agent = new QuizCommentaryAgent(
            headline: $card->headline,
            isReal: $card->is_real,
            userSaidReal: $userSaidReal,
            personaStyle: $commentary->persona_style,
        );

        $response = $agent->prompt('Kommentiere diese Quiz-Antwort.');

        $commentary->update([
            'status' => 'done',
            'text' => trim((string) $response),
            'prompt_used' => $agent->instructions(),
        ]);
    }

    public function failed(Throwable $exception): void
    {
        Commentary::where('id', $this->commentaryId)->update([
            'status' => 'failed',
            'text' => 'KI-Kommentar konnte nicht generiert werden.',
        ]);
    }
}
