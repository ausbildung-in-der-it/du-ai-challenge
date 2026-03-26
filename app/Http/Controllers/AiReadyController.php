<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Laravel\Cashier\Cashier;

class AiReadyController extends Controller
{
    public function index()
    {
        return Inertia::render('AiReady');
    }

    public function checkout()
    {
        $priceId = config('services.stripe.ai_ready_price_id');

        $session = Cashier::stripe()->checkout->sessions->create([
            'line_items' => [[
                'price' => $priceId,
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('ai-ready.success').'?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('ai-ready'),
            'billing_address_collection' => 'required',
            'tax_id_collection' => ['enabled' => true],
            'invoice_creation' => ['enabled' => true],
            'custom_fields' => [[
                'key' => 'company',
                'label' => ['type' => 'custom', 'custom' => 'Firma'],
                'type' => 'text',
            ]],
            'metadata' => [
                'product' => 'ai_ready_early_access',
            ],
        ]);

        return Inertia::location($session->url);
    }

    public function success()
    {
        return Inertia::render('AiReady/Danke');
    }
}
