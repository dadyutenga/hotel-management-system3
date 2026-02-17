<?php

namespace App\Contracts;

/**
 * PaymentProvider — common interface for all payment providers.
 *
 * Every adapter implements this contract so the PaymentEngine can
 * swap providers transparently.
 */
interface PaymentProvider
{
    /**
     * Get the provider's unique name.
     */
    public function name(): string;

    /**
     * Initiate a payment with the provider.
     *
     * @param  float   $amount      Amount to charge
     * @param  string  $currency    Currency code (e.g. TZS, USD)
     * @param  string  $method      Payment method (mobile, card, dynamic-qr, bank_transfer)
     * @param  array   $metadata    Booking/charge/guest information
     * @return array   Provider response with keys: success, reference, status, payment_url, payment_qr_code, payment_token, raw
     */
    public function initiatePayment(float $amount, string $currency, string $method, array $metadata = []): array;

    /**
     * Verify a payment by its provider reference.
     *
     * @param  string  $reference   Provider transaction reference
     * @return array   With keys: success, status (pending|completed|failed), raw
     */
    public function verifyPayment(string $reference): array;

    /**
     * Refund a payment (full or partial).
     *
     * @param  string      $reference  Provider transaction reference
     * @param  float|null  $amount     Refund amount (null = full)
     * @return array       With keys: success, refund_reference, raw
     */
    public function refundPayment(string $reference, ?float $amount = null): array;

    /**
     * Validate an incoming webhook request for authenticity.
     *
     * @param  array   $payload   Decoded JSON body
     * @param  array   $headers   Request headers
     * @return bool
     */
    public function validateWebhook(array $payload, array $headers): bool;

    /**
     * Parse a webhook payload into a normalized format.
     *
     * @param  array  $payload  Raw webhook body
     * @return array  Keys: event, reference, status, amount, currency, metadata, raw
     */
    public function parseWebhook(array $payload): array;

    /**
     * Get supported payment methods for this provider.
     *
     * @return array  e.g. ['mobile', 'card', 'dynamic-qr']
     */
    public function supportedMethods(): array;
}
