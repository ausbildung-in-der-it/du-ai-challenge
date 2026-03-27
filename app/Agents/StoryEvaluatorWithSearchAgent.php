<?php

namespace App\Agents;

use Laravel\Ai\Attributes\MaxSteps;
use Laravel\Ai\Attributes\MaxTokens;
use Laravel\Ai\Attributes\Temperature;
use Laravel\Ai\Attributes\Timeout;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Promptable;
use Laravel\Ai\Providers\Tools\WebSearch;

#[Temperature(0.3)]
#[MaxTokens(200)]
#[MaxSteps(3)]
#[Timeout(30)]
class StoryEvaluatorWithSearchAgent implements Agent, HasTools
{
    use Promptable;

    public function __construct(
        private readonly string $story,
    ) {}

    public function instructions(): string
    {
        return <<<PROMPT
        Du bist ein KI-Fakten-Checker MIT Zugang zum Internet. Du kannst die Story im Web recherchieren.

        WICHTIG: Nutze dein Web-Search-Tool um die Story zu verifizieren, bevor du antwortest!

        Analysiere die folgende Story und gib deine Einschätzung:
        - Bewertung: "echt" oder "erfunden"
        - Konfidenz: 1-10 (wie sicher bist du?)
        - Begründung: 1 kurzer Satz warum (erwähne ob du Quellen gefunden hast)

        Story: "{$this->story}"

        Antworte EXAKT in diesem Format (eine Zeile):
        [echt|erfunden] | Konfidenz: X/10 | Begründung

        Beispiel: echt | Konfidenz: 9/10 | Mehrere Nachrichtenquellen berichten darüber, u.a. The Register.
        PROMPT;
    }

    public function tools(): iterable
    {
        return [
            (new WebSearch)->max(2),
        ];
    }
}
