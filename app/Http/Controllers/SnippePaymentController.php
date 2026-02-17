<?php

namespace App\Http\Controllers;

use App\Services\Payment\PaymentEngine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * SnippePaymentController — handles Snippe webhooks.
 *
 * Webhook endpoint is public (no auth middleware) because Snippe's
 * servers call it directly. Signature verification is done via the
 * PaymentEngine → SnippeProvider::validateWebhook().
 */
class SnippePaymentController extends Controller
{
    /**
     * Handle incoming Snippe webhook.
     *
     * POST /payments/webhook/snippe
     */
    public function webhook(Request $request)
    {
        $payload = $request->all();
        $headers = $request->headers->all();

        // Flatten header arrays (Laravel returns headers as arrays)
        $flatHeaders = array_map(fn($h) => is_array($h) ? ($h[0] ?? '') : $h, $headers);

        Log::info('Snippe webhook received', [
            'type'      => $payload['type'] ?? 'unknown',
            'reference' => $payload['data']['reference'] ?? null,
        ]);

        try {
            $engine = new PaymentEngine();
            $payment = $engine->handleWebhook($payload, $flatHeaders);

            if ($payment) {
                return response()->json(['status' => 'ok', 'payment_id' => $payment->id], 200);
            }

            // Webhook valid but no matching payment found — still return 200
            // to prevent Snippe from retrying
            return response()->json(['status' => 'ok', 'message' => 'No matching payment'], 200);

        } catch (\Exception $e) {
            Log::error('Snippe webhook processing error', ['message' => $e->getMessage()]);
            return response()->json(['status' => 'error'], 500);
        }
    }
}
