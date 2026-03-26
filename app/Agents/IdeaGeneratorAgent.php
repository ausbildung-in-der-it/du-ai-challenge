<?php

namespace App\Agents;

use Laravel\Ai\Attributes\MaxTokens;
use Laravel\Ai\Attributes\Provider;
use Laravel\Ai\Attributes\Temperature;
use Laravel\Ai\Attributes\Timeout;
use Laravel\Ai\Attributes\UseSmartestModel;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Enums\Lab;
use Laravel\Ai\Promptable;

#[Provider(Lab::Anthropic)]
#[UseSmartestModel]
#[Temperature(0.7)]
#[MaxTokens(2000)]
#[Timeout(60)]
class IdeaGeneratorAgent implements Agent
{
    use Promptable;

    public function instructions(): string
    {
        return <<<'PROMPT'
        Du bist ein KI-Strategieberater für deutsche Unternehmen. Der Nutzer beschreibt sein Unternehmen oder eine Herausforderung. Generiere 3-5 konkrete, umsetzbare Ideen, wie KI-Agenten diesem Unternehmen helfen können. Sei spezifisch, nenne Tools (Claude, Copilot, ChatGPT, etc.) und schätze den Zeitgewinn. Formatiere mit Markdown. Antworte auf Deutsch. Halte es praxisnah und realistisch — keine Science-Fiction. Denk an: Wir geben eine Angel, keinen Fisch. Die Ideen sollen zeigen, was möglich wird, wenn das Team KI-Kompetenz aufbaut.
        PROMPT;
    }
}
