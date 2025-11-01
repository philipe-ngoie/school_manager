<?php

namespace App\Contracts;

interface PaymentGatewayInterface
{
    /**
     * Create a payment session
     *
     * @param array $data Payment data (amount, currency, customer info, etc.)
     * @return array Payment session details including payment URL
     */
    public function createPaymentSession(array $data): array;

    /**
     * Verify a payment
     *
     * @param string $transactionId Transaction ID from the payment provider
     * @return array Payment verification details
     */
    public function verifyPayment(string $transactionId): array;

    /**
     * Handle webhook from payment provider
     *
     * @param array $payload Webhook payload
     * @return array Processed webhook data
     */
    public function handleWebhook(array $payload): array;

    /**
     * Process a refund
     *
     * @param string $transactionId Original transaction ID
     * @param float $amount Amount to refund
     * @return array Refund details
     */
    public function processRefund(string $transactionId, float $amount): array;
}
