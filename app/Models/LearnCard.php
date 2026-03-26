<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['journey_block_id', 'title', 'content', 'icon', 'position'])]
class LearnCard extends Model
{
    /** @return BelongsTo<JourneyBlock, $this> */
    public function block(): BelongsTo
    {
        return $this->belongsTo(JourneyBlock::class, 'journey_block_id');
    }
}
