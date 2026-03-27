<?php

use App\Models\JourneySession;
use App\Models\LearningJourney;
use App\Models\NewsletterLead;

it('stores a homepage newsletter lead', function () {
    $response = $this->postJson(route('newsletter-leads.store'), [
        'email' => '  Test@Example.com ',
        'linkedin_url' => 'https://www.linkedin.com/in/noel-lang',
        'privacy_accepted' => true,
        'source' => 'homepage',
        'completed_challenge' => false,
    ]);

    $response->assertCreated()
        ->assertJson([
            'created' => true,
            'completed_challenge' => false,
        ]);

    $lead = NewsletterLead::query()->firstWhere('email', 'test@example.com');

    expect($lead)
        ->not->toBeNull()
        ->source->toBe('homepage')
        ->completed_challenge->toBeFalse()
        ->linkedin_url->toBe('https://www.linkedin.com/in/noel-lang');
});

it('upgrades an existing lead after challenge completion', function () {
    $lead = NewsletterLead::factory()->create([
        'email' => 'lead@example.com',
        'source' => 'homepage',
        'completed_challenge' => false,
        'linkedin_url' => null,
    ]);

    $journey = LearningJourney::query()->create([
        'title' => 'Real or Fake?',
        'slug' => 'real-or-fake',
        'description' => 'Challenge',
    ]);

    $session = JourneySession::query()->create([
        'learning_journey_id' => $journey->id,
    ]);

    $response = $this->postJson(route('newsletter-leads.store'), [
        'email' => 'lead@example.com',
        'linkedin_url' => 'https://www.linkedin.com/in/updated-profile',
        'privacy_accepted' => true,
        'source' => 'challenge_complete',
        'journey_slug' => $journey->slug,
        'journey_session_nanoid' => $session->nanoid,
        'completed_challenge' => true,
    ]);

    $response->assertOk()
        ->assertJson([
            'created' => false,
            'completed_challenge' => true,
        ]);

    $lead->refresh();

    expect($lead->source)->toBe('challenge_complete')
        ->and($lead->completed_challenge)->toBeTrue()
        ->and($lead->journey_slug)->toBe('real-or-fake')
        ->and($lead->journey_session_nanoid)->toBe($session->nanoid)
        ->and($lead->linkedin_url)->toBe('https://www.linkedin.com/in/updated-profile');
});

it('validates that an email address is present', function () {
    $response = $this->postJson(route('newsletter-leads.store'), [
        'privacy_accepted' => true,
        'source' => 'homepage',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

it('requires accepting the privacy policy', function () {
    $response = $this->postJson(route('newsletter-leads.store'), [
        'email' => 'lead@example.com',
        'source' => 'homepage',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['privacy_accepted']);
});
