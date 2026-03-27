<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class ChoiceCardData extends Data
{
    public function __construct(
        public int $id,
        public string $question,
        /** @var string[] */
        public array $options,
        public int $correct_index,
        public string $explanation,
        public int $position,
    ) {}
}
