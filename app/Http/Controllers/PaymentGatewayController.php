<?php

namespace App\Http\Controllers;

use App\Services\Payment\WebhookService;
use App\Services\Payment\Providers\MidtransAdapter;
use App\Services\Payment\Providers\SimulatorAdapter;
use Illuminate\Http\Request;

/**
 * Webhook endpoints for both payment providers (§10.2).
 * Both routes are exempt from CSRF (see bootstrap/app.php middleware config).
 * Authentication is handled by the provider adapters (signature_key / HMAC).
 */
class PaymentGatewayController extends Controller
{
    public function __construct(
        private readonly WebhookService   $core,
        private readonly MidtransAdapter  $midtrans,
        private readonly SimulatorAdapter $simulator,
    ) {}

    /** POST /webhooks/midtrans */
    public function midtrans(Request $request)
    {
        if (!$this->midtrans->isEnabled()) {
            return response()->json(['message' => 'Midtrans not configured.'], 503);
        }

        if (!$this->midtrans->authenticate($request)) {
            return $this->core->rejectUnauthenticated('midtrans', $request->all())->toJsonResponse();
        }

        $notification = $this->midtrans->normalize($request);
        return $this->core->process($notification)->toJsonResponse();
    }

    /** POST /webhooks/simulator */
    public function simulator(Request $request)
    {
        if (!$this->simulator->isEnabled()) {
            return response()->json(['message' => 'Simulator is disabled.'], 403);
        }

        if (!$this->simulator->authenticate($request)) {
            return $this->core->rejectUnauthenticated('simulator', $request->all())->toJsonResponse();
        }

        $notification = $this->simulator->normalize($request);
        return $this->core->process($notification)->toJsonResponse();
    }
}
