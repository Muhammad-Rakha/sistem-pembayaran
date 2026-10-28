<?php

namespace App\Services\Payment\Providers;

use App\Services\Payment\NormalizedNotification;
use Illuminate\Http\Request;

interface ProviderAdapterInterface
{
    /**
     * Verify the notification's authentication (signature, HMAC, etc.).
     * Returns true if valid.
     */
    public function authenticate(Request $request): bool;

    /**
     * Parse the raw request into a NormalizedNotification.
     * Only called after authenticate() returns true.
     */
    public function normalize(Request $request): NormalizedNotification;

    /**
     * Whether this provider is enabled in the current environment.
     */
    public function isEnabled(): bool;
}
