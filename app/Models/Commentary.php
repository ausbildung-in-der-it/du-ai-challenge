<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

#[Fillable([
    'nanoid', 'journey_session_id', 'quiz_card_id',
    'persona_style', 'status', 'text', 'prompt_used',
])]
class Commentary extends Model
{
    protected function casts(): array
    {
        return [];
    }

    public function getRouteKeyName(): string
    {
        return 'nanoid';
    }

    protected static function booted(): void
    {
        static::creating(function (self $commentary) {
            $commentary->nanoid ??= Str::random(21);
        });
    }

    /** @return BelongsTo<JourneySession, $this> */
    public function session(): BelongsTo
    {
        return $this->belongsTo(JourneySession::class, 'journey_session_id');
    }

    /** @return BelongsTo<QuizCard, $this> */
    public function quizCard(): BelongsTo
    {
        return $this->belongsTo(QuizCard::class);
    }
}
