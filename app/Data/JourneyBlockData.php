<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class JourneyBlockData extends Data
{
    public function __construct(
        public int $id,
        public string $type,
        public int $position,
        /** @var array<string, mixed>|null */
        public ?array $config,
        /** @var QuizCardData[] */
        public array $quiz_cards,
        /** @var LearnCardData[] */
        public array $learn_cards,
    ) {}
}
