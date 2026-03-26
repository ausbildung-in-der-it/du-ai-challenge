<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class LearnCardData extends Data
{
    public function __construct(
        public int $id,
        public string $title,
        public string $content,
        public ?string $icon,
        public int $position,
    ) {}
}
