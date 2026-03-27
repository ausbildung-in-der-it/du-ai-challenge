<?php

namespace Database\Factories;

use App\Models\NewsletterLead;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<NewsletterLead>
 */
class NewsletterLeadFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'email' => fake()->unique()->safeEmail(),
            'linkedin_url' => fake()->optional()->url(),
            'source' => fake()->randomElement(['homepage', 'challenge_complete']),
            'journey_slug' => fake()->optional()->slug(),
            'journey_session_nanoid' => fake()->optional()->regexify('[A-Za-z0-9]{21}'),
            'completed_challenge' => fake()->boolean(),
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
        ];
    }
}
