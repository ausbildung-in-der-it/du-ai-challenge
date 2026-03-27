<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['hash', 'short_hash', 'message', 'committed_at', 'minutes_in'])]
class GitCommit extends Model
{
    protected function casts(): array
    {
        return [
            'committed_at' => 'datetime',
            'minutes_in' => 'integer',
        ];
    }
}
