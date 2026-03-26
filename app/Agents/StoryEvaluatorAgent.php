<?php

namespace App\Agents;

use Laravel\Ai\Attributes\MaxTokens;
use Laravel\Ai\Attributes\Temperature;
use Laravel\Ai\Attributes\Timeout;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Promptable;

#[Temperature(0.3)]
#[MaxTokens(200)]
#[Timeout(20)]
class StoryEvaluatorAgent implements Agent
{
    use Promptable;

    public function __construct(
        private readonly string $story,
    ) {}

    public function instructions(): string
    {
        return <<<PROMPT
        Du bist ein KI-Fakten-Checker. Du bewertest, ob eine Story wahr oder erfunden ist.

        Analysiere die folgende Story und gib deine Einschätzung:
        - Bewertung: "echt" oder "erfunden"
        - Konfidenz: 1-10 (wie sicher bist du?)
        - Begründung: 1 kurzer Satz warum

        Story: "{$this->story}"

        Antworte EXAKT in diesem Format (eine Zeile):
        [echt|erfunden] | Konfidenz: X/10 | Begründung

        Beispiel: echt | Konfidenz: 8/10 | Klingt nach einem typischen Chatbot-Fehler im Kundenservice.
        PROMPT;
    }
}
