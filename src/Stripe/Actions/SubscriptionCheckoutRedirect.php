<?php

namespace Opcodes\Spike\Stripe\Actions;

use Illuminate\Http\RedirectResponse;
use Opcodes\Spike\Facades\Spike;
use Opcodes\Spike\Stripe\Interfaces\SubscriptionCheckoutRedirectInterface;
use Opcodes\Spike\Stripe\PaymentGateway;
use Opcodes\Spike\SubscriptionPlan;

class SubscriptionCheckoutRedirect implements SubscriptionCheckoutRedirectInterface
{
    public function handle(SubscriptionPlan $plan): RedirectResponse
    {
        $subscriptionBuilder = Spike::resolve()->newSubscription(
            PaymentGateway::$subscriptionName,
            $plan->payment_provider_price_id
        );

        if (Spike::stripeAllowDiscounts()) {
            $subscriptionBuilder = $subscriptionBuilder->allowPromotionCodes();
        }

        $options = [
            'success_url' => route('spike.subscribe', ['success' => true]),
            'cancel_url' => route('spike.subscribe', ['canceled' => true]),
        ];

        if ($locale = config('spike.stripe.checkout.default_locale')) {
            $options['locale'] = $locale;
        }

        $checkout = $subscriptionBuilder->checkout($options);

        // Same rationale as CartCheckout::redirectToStripeCheckout(): Livewire must receive
        // a real RedirectResponse, not Cashier's redirect() / Responsable indirection.
        return new RedirectResponse($checkout->asStripeCheckoutSession()->url, 303);
    }
}
