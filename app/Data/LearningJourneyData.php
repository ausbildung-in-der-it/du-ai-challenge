<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class LearningJourneyData extends Data
{
    public function __construct(
        public int $id,
        public string $title,
        public string $slug,
        public ?string $description,
        /** @var JourneyBlockData[] */
        public array $blocks,
    ) {}
}
