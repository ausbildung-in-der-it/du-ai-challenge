<?php

namespace App\Models;

use Database\Factories\NewsletterLeadFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'email',
    'linkedin_url',
    'source',
    'journey_slug',
    'journey_session_nanoid',
    'completed_challenge',
    'ip_address',
    'user_agent',
])]
class NewsletterLead extends Model
{
    /** @use HasFactory<NewsletterLeadFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'completed_challenge' => 'boolean',
        ];
    }
}
