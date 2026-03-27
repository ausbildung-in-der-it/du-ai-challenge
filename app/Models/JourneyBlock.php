<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['learning_journey_id', 'type', 'position', 'config'])]
class JourneyBlock extends Model
{
    protected function casts(): array
    {
        return [
            'config' => 'array',
        ];
    }

    /** @return BelongsTo<LearningJourney, $this> */
    public function journey(): BelongsTo
    {
        return $this->belongsTo(LearningJourney::class, 'learning_journey_id');
    }

    /** @return HasMany<QuizCard, $this> */
    public function quizCards(): HasMany
    {
        return $this->hasMany(QuizCard::class)->orderBy('position');
    }

    /** @return HasMany<LearnCard, $this> */
    public function learnCards(): HasMany
    {
        return $this->hasMany(LearnCard::class)->orderBy('position');
    }

    /** @return HasMany<ChoiceCard, $this> */
    public function choiceCards(): HasMany
    {
        return $this->hasMany(ChoiceCard::class)->orderBy('position');
    }
}
