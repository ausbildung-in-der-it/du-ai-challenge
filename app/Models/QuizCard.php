<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['journey_block_id', 'category', 'headline', 'story', 'date_label', 'is_real', 'explanation', 'sources', 'position'])]
class QuizCard extends Model
{
    protected function casts(): array
    {
        return [
            'is_real' => 'boolean',
            'sources' => 'array',
        ];
    }

    /** @return BelongsTo<JourneyBlock, $this> */
    public function block(): BelongsTo
    {
        return $this->belongsTo(JourneyBlock::class, 'journey_block_id');
    }
}
