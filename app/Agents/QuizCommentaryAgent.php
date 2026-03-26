<?php

namespace App\Agents;

use Laravel\Ai\Attributes\MaxTokens;
use Laravel\Ai\Attributes\Model;
use Laravel\Ai\Attributes\Provider;
use Laravel\Ai\Attributes\Temperature;
use Laravel\Ai\Attributes\Timeout;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Enums\Lab;
use Laravel\Ai\Promptable;

#[Provider(Lab::Anthropic)]
#[Model('claude-haiku-4-5-20251001')]
#[Temperature(0.8)]
#[MaxTokens(150)]
#[Timeout(15)]
class QuizCommentaryAgent implements Agent
{
    use Promptable;

    public function __construct(
        private readonly string $headline,
        private readonly bool $isReal,
        private readonly bool $userSaidReal,
    ) {}

    public function instructions(): string
    {
        $isCorrect = $this->userSaidReal === $this->isReal;
        $truth = $this->isReal ? 'wirklich passiert' : 'erfunden';
        $result = $isCorrect ? 'RICHTIG getippt' : 'FALSCH getippt';

        return <<<PROMPT
        Du bist ein witziger, knapper KI-Kommentator auf dem data:unplugged Festival 2026 in Münster.

        Kontext: Eine Quiz-Karte wurde beantwortet.
        - Headline: "{$this->headline}"
        - Wahrheit: {$truth}
        - Ergebnis: {$result}

        Schreibe GENAU 1 kurzen, witzigen Satz als Kommentar. Maximal 20 Wörter.
        - Sei überraschend, pointiert und leicht provokant.
        - Beziehe dich auf den konkreten Inhalt der Story.
        - Kein Emoji, kein Hashtag, keine Anführungszeichen.
        - Schreibe NUR den einen Satz, sonst nichts.
        PROMPT;
    }
}
