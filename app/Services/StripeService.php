<?php

namespace App\Services;

use Stripe\StripeClient;

class StripeService
{
    protected $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(env('STRIPE_SECRET'));
    }

    public function createToken(array $cardData)
    {
        try {
            $token = $this->stripe->tokens->create([
                'card' => [
                    'name' => $cardData['card_name'],
                    'number' => $cardData['card_number'],
                    'exp_month' => $cardData['exp_month'],
                    'exp_year' => $cardData['exp_year'],
                    'cvc' => $cardData['cvc'],
                ],
            ]);

            return $token->id;
        } catch (\Exception $e) {
            throw new \Exception('Failed to create Stripe token: ' . $e->getMessage());
        }
    }

    public function createCharge($amount, $currency, $source)
    {
        try {
            $charge = $this->stripe->charges->create([
                "amount" => $amount,
                "currency" => $currency,
                "source" => $source,
            ]);

            return $charge;
        } catch (\Exception $e) {
            throw new \Exception('Failed to create Stripe charge: ' . $e->getMessage());
        }
    }
}
