<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Laravel\Cashier\Cashier;

class AiReadyController extends Controller
{
    public function index()
    {
        Log::info('AiReadyController@index');

        return Inertia::render('AiReady');
    }

    public function checkout()
    {
        $priceId = config('services.stripe.ai_ready_price_id');

        Log::info('AiReadyController@checkout', ['price_id' => $priceId]);

        $session = Cashier::stripe()->checkout->sessions->create([
            'line_items' => [[
                'price' => $priceId,
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('ai-ready.success').'?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('ai-ready'),
            'automatic_tax' => ['enabled' => true],
            'billing_address_collection' => 'required',
            'tax_id_collection' => ['enabled' => true],
            'invoice_creation' => ['enabled' => true],
            'metadata' => [
                'product' => 'ai_ready_early_access',
            ],
        ]);

        return Inertia::location($session->url);
    }

    public function success(Request $request)
    {
        $sessionId = $request->query('session_id');

        Log::info('AiReadyController@success', ['has_session_id' => (bool) $sessionId]);

        if (! $sessionId) {
            return redirect()->route('ai-ready');
        }

        try {
            $session = Cashier::stripe()->checkout->sessions->retrieve($sessionId);

            if ($session->payment_status !== 'paid') {
                return redirect()->route('ai-ready');
            }
        } catch (\Exception $e) {
            Log::error('AiReadyController@success: failed to retrieve Stripe session', ['error' => $e->getMessage()]);

            return redirect()->route('ai-ready');
        }

        return Inertia::render('AiReady/Danke', [
            'customerEmail' => $session->customer_details?->email,
        ]);
    }
}
