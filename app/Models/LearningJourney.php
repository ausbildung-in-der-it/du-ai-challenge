<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['title', 'slug', 'description'])]
class LearningJourney extends Model
{
    /** @return HasMany<JourneyBlock, $this> */
    public function blocks(): HasMany
    {
        return $this->hasMany(JourneyBlock::class)->orderBy('position');
    }
}
