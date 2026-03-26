<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Laravel\Cashier\Cashier;

class StripeProductSeeder extends Seeder
{
    public function run(): void
    {
        $stripe = Cashier::stripe();

        // Check if product already exists
        $products = $stripe->products->search([
            'query' => "metadata['app_id']:'ai_ready_early_access'",
        ]);

        if ($products->data) {
            $product = $products->data[0];
            $this->command->info("Product already exists: {$product->id}");
        } else {
            $product = $stripe->products->create([
                'name' => 'AI Ready – Early Access',
                'description' => '7 Module (AI Literacy + AI Security) · Unbegrenzte User-Lizenzen für 12 Monate · Halbtägiger Workshop (3h) · EU AI Act Compliance-Zertifikate',
                'metadata' => [
                    'app_id' => 'ai_ready_early_access',
                ],
                'tax_code' => 'txcd_10202000', // SaaS / Electronic services
            ]);
            $this->command->info("Product created: {$product->id}");
        }

        // Check if price already exists
        $prices = $stripe->prices->search([
            'query' => "product:'{$product->id}' active:'true'",
        ]);

        if ($prices->data) {
            $price = $prices->data[0];
            $this->command->info("Price already exists: {$price->id}");
        } else {
            $price = $stripe->prices->create([
                'product' => $product->id,
                'unit_amount' => 199000, // 1.990,00 EUR in cents
                'currency' => 'eur',
                'tax_behavior' => 'exclusive', // Netto price, tax added on top
                'metadata' => [
                    'app_id' => 'ai_ready_early_access',
                ],
            ]);
            $this->command->info("Price created: {$price->id}");
        }

        $this->command->newLine();
        $this->command->info('Add this to your .env:');
        $this->command->line("AI_READY_STRIPE_PRICE_ID={$price->id}");
    }
}
