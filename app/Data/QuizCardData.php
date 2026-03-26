<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class QuizCardData extends Data
{
    public function __construct(
        public int $id,
        public string $category,
        public string $headline,
        public string $story,
        public string $date_label,
        public bool $is_real,
        public string $explanation,
        /** @var SourceData[] */
        public array $sources,
        public int $position,
    ) {}
}
