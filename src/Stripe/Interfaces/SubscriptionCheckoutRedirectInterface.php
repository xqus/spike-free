<?php

namespace Opcodes\Spike\Stripe\Interfaces;

use Illuminate\Http\RedirectResponse;
use Opcodes\Spike\SubscriptionPlan;

interface SubscriptionCheckoutRedirectInterface
{
    public function handle(SubscriptionPlan $plan): RedirectResponse;
}
