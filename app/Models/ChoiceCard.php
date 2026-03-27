<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['journey_block_id', 'question', 'options', 'correct_index', 'explanation', 'position'])]
class ChoiceCard extends Model
{
    protected function casts(): array
    {
        return [
            'options' => 'array',
            'correct_index' => 'integer',
        ];
    }

    /** @return BelongsTo<JourneyBlock, $this> */
    public function block(): BelongsTo
    {
        return $this->belongsTo(JourneyBlock::class, 'journey_block_id');
    }
}
