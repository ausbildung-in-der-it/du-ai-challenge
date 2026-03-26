<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Fillable([
    'nanoid', 'learning_journey_id', 'current_block', 'current_item',
    'answers', 'persona_style', 'persona_prompt_shown', 'completed_at',
])]
class JourneySession extends Model
{
    protected function casts(): array
    {
        return [
            'answers' => 'array',
            'persona_prompt_shown' => 'boolean',
            'completed_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'nanoid';
    }

    protected static function booted(): void
    {
        static::creating(function (self $session) {
            $session->nanoid ??= Str::random(21);
        });
    }

    /** @return BelongsTo<LearningJourney, $this> */
    public function journey(): BelongsTo
    {
        return $this->belongsTo(LearningJourney::class, 'learning_journey_id');
    }

    /** @return HasMany<Commentary, $this> */
    public function commentaries(): HasMany
    {
        return $this->hasMany(Commentary::class);
    }
}
