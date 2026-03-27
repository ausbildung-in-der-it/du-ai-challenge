<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNewsletterLeadRequest;
use App\Models\NewsletterLead;
use Illuminate\Http\JsonResponse;

class NewsletterLeadController extends Controller
{
    public function store(StoreNewsletterLeadRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $newsletterLead = NewsletterLead::query()->firstOrNew([
            'email' => $validated['email'],
        ]);

        $newsletterLead->source = $validated['source'];
        $newsletterLead->journey_slug = $validated['journey_slug'] ?? $newsletterLead->journey_slug;
        $newsletterLead->journey_session_nanoid = $validated['journey_session_nanoid'] ?? $newsletterLead->journey_session_nanoid;
        $newsletterLead->completed_challenge = ($validated['completed_challenge'] ?? false) || $newsletterLead->completed_challenge;
        $newsletterLead->ip_address = $request->ip();
        $newsletterLead->user_agent = $request->userAgent();

        if (filled($validated['linkedin_url'] ?? null)) {
            $newsletterLead->linkedin_url = $validated['linkedin_url'];
        }

        $wasRecentlyCreated = ! $newsletterLead->exists;

        $newsletterLead->save();

        return response()->json([
            'lead_id' => $newsletterLead->id,
            'created' => $wasRecentlyCreated,
            'completed_challenge' => $newsletterLead->completed_challenge,
        ], $wasRecentlyCreated ? 201 : 200);
    }
}
