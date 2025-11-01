# Payment Integration Guide

This document explains the payment system integration in the School Manager application, with focus on CinetPay integration and how to add additional payment providers.

## Table of Contents

1. [Overview](#overview)
2. [CinetPay Integration](#cinetpay-integration)
3. [Payment Flow](#payment-flow)
4. [Webhook Handling](#webhook-handling)
5. [Adding New Payment Providers](#adding-new-payment-providers)
6. [Testing](#testing)
7. [Troubleshooting](#troubleshooting)

## Overview

The School Manager payment system supports multiple payment gateways through a unified interface (`PaymentGatewayInterface`). Currently implemented:

- **CinetPay**: Primary payment gateway (fully implemented)
- **Stripe**: Stub implementation showing how to add additional providers

### Architecture

```
PaymentGatewayInterface (Contract)
    ├── CinetPayService (Implementation)
    └── StripeAdapter (Stub Implementation)
```

## CinetPay Integration

CinetPay is a payment gateway popular in West Africa, supporting multiple payment methods including Mobile Money, credit cards, and bank transfers.

### Configuration

1. **Sign up for CinetPay**
   - Visit [CinetPay](https://cinetpay.com)
   - Create an account and get your credentials

2. **Configure Environment Variables**

Add to your `.env` file:

```env
CINETPAY_SITE_ID=your_site_id_here
CINETPAY_API_KEY=your_api_key_here
CINETPAY_SECRET_KEY=your_secret_key_here
CINETPAY_MODE=sandbox
CINETPAY_NOTIFY_URL=https://your-domain.com/api/payments/webhook/cinetpay
CINETPAY_RETURN_URL=https://your-domain.com/payments/callback
```

3. **Sandbox vs Production**

- **Sandbox**: Use for testing (set `CINETPAY_MODE=sandbox`)
- **Production**: Use for live payments (set `CINETPAY_MODE=production`)

### Test Credentials (Sandbox)

For testing in sandbox mode, CinetPay provides test credentials:
- Site ID: Available in your CinetPay dashboard
- API Key: Available in your CinetPay dashboard
- Test cards and mobile money numbers are provided in CinetPay documentation

## Payment Flow

### 1. Initiate Payment

**Endpoint**: `POST /api/payments/initiate`

**Request**:
```json
{
  "invoice_id": 1,
  "payment_method": "cinetpay",
  "amount": 1000.00
}
```

**Response**:
```json
{
  "success": true,
  "payment": {
    "id": 1,
    "payment_reference": "PAY-20250101-ABCD1234",
    "status": "pending",
    ...
  },
  "payment_url": "https://checkout.cinetpay.com/payment/..."
}
```

### 2. Redirect to Payment Gateway

The frontend receives the `payment_url` and redirects the user (or opens a WebView in mobile apps) to complete the payment.

### 3. User Completes Payment

The user completes payment on CinetPay's secure payment page.

### 4. Webhook Notification

CinetPay sends a webhook notification to `CINETPAY_NOTIFY_URL`:

**Webhook Endpoint**: `POST /api/payments/webhook/cinetpay` (public, no auth required)

**Webhook Payload**:
```json
{
  "cpm_trans_id": "PAY-20250101-ABCD1234",
  "cpm_result": "00",
  "signature": "webhook_signature",
  ...
}
```

### 5. Verify Payment

**Endpoint**: `POST /api/payments/verify`

**Request**:
```json
{
  "payment_reference": "PAY-20250101-ABCD1234"
}
```

**Response**:
```json
{
  "success": true,
  "message": "Payment verified and completed",
  "payment": {
    "status": "completed",
    "paid_at": "2025-01-01T12:00:00Z",
    ...
  }
}
```

## Webhook Handling

### Webhook Security

The webhook endpoint verifies requests using signature validation:

```php
protected function verifyWebhookSignature(array $payload): bool
{
    if (!isset($payload['signature'])) {
        return false;
    }
    
    $expectedSignature = hash_hmac('sha256', json_encode($payload), $this->secretKey);
    return hash_equals($expectedSignature, $payload['signature']);
}
```

### Idempotency

Payment updates are idempotent using the `provider_payment_id` unique constraint:

- Each payment provider transaction ID is stored only once
- Duplicate webhook calls won't create duplicate payments
- Status updates are conditional (only update if not already completed)

### Webhook Workflow

1. **Receive webhook** → Verify signature
2. **Extract transaction ID** → Find payment record
3. **Verify with provider API** → Confirm payment status
4. **Update database** → Mark payment as completed
5. **Update invoice** → Update paid amount and status
6. **Create transaction** → Record financial transaction
7. **Send notification** → Email receipt to parent (future enhancement)

## Adding New Payment Providers

To add a new payment gateway (e.g., PayPal, Flutterwave):

### Step 1: Implement the Interface

Create a new service class implementing `PaymentGatewayInterface`:

```php
<?php

namespace App\Services\Payment;

use App\Contracts\PaymentGatewayInterface;

class FlutterwaveService implements PaymentGatewayInterface
{
    public function createPaymentSession(array $data): array
    {
        // Implement payment session creation
    }
    
    public function verifyPayment(string $transactionId): array
    {
        // Implement payment verification
    }
    
    public function handleWebhook(array $payload): array
    {
        // Implement webhook handling
    }
    
    public function processRefund(string $transactionId, float $amount): array
    {
        // Implement refund processing
    }
}
```

### Step 2: Configure Services

Add configuration to `config/services.php`:

```php
'flutterwave' => [
    'public_key' => env('FLUTTERWAVE_PUBLIC_KEY'),
    'secret_key' => env('FLUTTERWAVE_SECRET_KEY'),
    'webhook_secret' => env('FLUTTERWAVE_WEBHOOK_SECRET'),
],
```

### Step 3: Update Payment Controller

Modify `PaymentController::initiatePayment()` to support the new gateway:

```php
$gateway = match($validated['payment_method']) {
    'cinetpay' => new CinetPayService(),
    'stripe' => new StripeAdapter(),
    'flutterwave' => new FlutterwaveService(),
    default => throw new \Exception('Unsupported payment method'),
};
```

### Step 4: Add Webhook Route

Add a public webhook route in `routes/api.php`:

```php
Route::post('/payments/webhook/flutterwave', [PaymentController::class, 'webhookFlutterwave']);
```

## Testing

### Unit Tests

Test payment service methods:

```php
public function test_payment_initiation()
{
    $service = new CinetPayService();
    $result = $service->createPaymentSession([
        'transaction_id' => 'TEST-123',
        'amount' => 1000,
        'currency' => 'XOF',
        // ...
    ]);
    
    $this->assertTrue($result['success']);
    $this->assertArrayHasKey('payment_url', $result);
}
```

### Integration Tests

Test complete payment flow:

```php
public function test_complete_payment_flow()
{
    // 1. Create invoice
    $invoice = Invoice::factory()->create();
    
    // 2. Initiate payment
    $response = $this->postJson('/api/payments/initiate', [
        'invoice_id' => $invoice->id,
        'payment_method' => 'cinetpay',
        'amount' => $invoice->total_amount,
    ]);
    
    $response->assertStatus(200);
    
    // 3. Simulate webhook
    // 4. Verify payment
    // 5. Check invoice status
}
```

### Manual Testing with Sandbox

1. Create a test invoice in the system
2. Initiate payment via API
3. Use CinetPay test cards/mobile numbers
4. Complete payment
5. Verify webhook is received
6. Check payment and invoice status updated

## Troubleshooting

### Common Issues

#### 1. Webhook Not Received

**Symptoms**: Payment completed but invoice not updated

**Solutions**:
- Check `CINETPAY_NOTIFY_URL` is publicly accessible
- Verify webhook URL in CinetPay dashboard
- Check server logs for incoming webhook requests
- Ensure no firewall blocking CinetPay IPs

#### 2. Signature Verification Failed

**Symptoms**: Webhook received but signature validation fails

**Solutions**:
- Verify `CINETPAY_SECRET_KEY` matches dashboard
- Check payload format hasn't changed
- Log the received payload and expected signature

#### 3. Payment Stuck in Pending

**Symptoms**: User completed payment but status remains pending

**Solutions**:
- Manually verify payment via `POST /api/payments/verify`
- Check CinetPay dashboard for transaction status
- Review webhook delivery status in CinetPay dashboard

#### 4. Duplicate Payments

**Symptoms**: Same payment recorded multiple times

**Solutions**:
- Verify `provider_payment_id` unique constraint exists
- Check webhook idempotency logic
- Review database logs for constraint violations

### Debugging

Enable detailed logging:

```php
// In CinetPayService methods
Log::info('CinetPay API Request', [
    'url' => $url,
    'payload' => $payload
]);

Log::info('CinetPay API Response', [
    'status' => $response->status(),
    'body' => $response->json()
]);
```

### Support

- **CinetPay Documentation**: https://docs.cinetpay.com/
- **CinetPay Support**: support@cinetpay.com
- **School Manager Issues**: GitHub Issues

## Security Best Practices

1. **Never expose API keys** in client-side code
2. **Always verify webhook signatures** before processing
3. **Use HTTPS** for all payment-related endpoints
4. **Implement rate limiting** on webhook endpoints
5. **Log all payment activities** for audit trail
6. **Validate all payment amounts** before processing
7. **Use environment variables** for sensitive credentials
8. **Regularly rotate API keys** in production

## Multi-Currency Support

The system supports multiple currencies:
- **USD**: US Dollar
- **XOF**: West African CFA Franc
- **EUR**: Euro

Currency conversion rates are stored in the `currency_rates` table. Update rates regularly using the artisan command (to be implemented).

## Refunds

Refund flow:

1. **Request refund**: `POST /api/invoices/{invoice}/refund`
2. **Admin processes**: `POST /api/refunds/{refund}/process`
3. **Provider API called**: Refund created in payment gateway
4. **Status updated**: Refund marked as completed/failed
5. **Invoice adjusted**: Paid amount reduced

Note: CinetPay refund API support may vary. Check current API documentation.
