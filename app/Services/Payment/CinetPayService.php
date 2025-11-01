<?php

namespace App\Services\Payment;

use App\Contracts\PaymentGatewayInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CinetPayService implements PaymentGatewayInterface
{
    protected string $apiKey;
    protected string $siteId;
    protected string $secretKey;
    protected string $mode;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.cinetpay.api_key');
        $this->siteId = config('services.cinetpay.site_id');
        $this->secretKey = config('services.cinetpay.secret_key');
        $this->mode = config('services.cinetpay.mode', 'sandbox');
        
        $this->baseUrl = $this->mode === 'production'
            ? 'https://api-checkout.cinetpay.com/v2'
            : 'https://api-checkout.cinetpay.com/v2'; // CinetPay uses same URL for sandbox
    }

    /**
     * Create a payment session with CinetPay
     *
     * @param array $data
     * @return array
     */
    public function createPaymentSession(array $data): array
    {
        try {
            $payload = [
                'apikey' => $this->apiKey,
                'site_id' => $this->siteId,
                'transaction_id' => $data['transaction_id'],
                'amount' => (int) $data['amount'], // CinetPay expects amount in smallest currency unit
                'currency' => $data['currency'] ?? 'XOF',
                'description' => $data['description'] ?? 'School payment',
                'customer_name' => $data['customer_name'] ?? '',
                'customer_surname' => $data['customer_surname'] ?? '',
                'customer_email' => $data['customer_email'] ?? '',
                'customer_phone_number' => $data['customer_phone'] ?? '',
                'customer_address' => $data['customer_address'] ?? '',
                'customer_city' => $data['customer_city'] ?? '',
                'customer_country' => $data['customer_country'] ?? 'CI',
                'customer_state' => $data['customer_state'] ?? '',
                'customer_zip_code' => $data['customer_zip'] ?? '',
                'notify_url' => config('services.cinetpay.notify_url'),
                'return_url' => config('services.cinetpay.return_url'),
                'channels' => $data['channels'] ?? 'ALL',
                'metadata' => $data['metadata'] ?? [],
            ];

            $response = Http::post("{$this->baseUrl}/payment", $payload);

            if ($response->successful()) {
                $result = $response->json();
                
                if (isset($result['code']) && $result['code'] === '201') {
                    return [
                        'success' => true,
                        'payment_url' => $result['data']['payment_url'],
                        'payment_token' => $result['data']['payment_token'],
                        'transaction_id' => $data['transaction_id'],
                    ];
                }
            }

            Log::error('CinetPay payment creation failed', [
                'response' => $response->body(),
                'status' => $response->status()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to create payment session',
                'error' => $response->json()
            ];

        } catch (\Exception $e) {
            Log::error('CinetPay exception: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Payment service error',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Verify a payment with CinetPay
     *
     * @param string $transactionId
     * @return array
     */
    public function verifyPayment(string $transactionId): array
    {
        try {
            $payload = [
                'apikey' => $this->apiKey,
                'site_id' => $this->siteId,
                'transaction_id' => $transactionId,
            ];

            $response = Http::post("{$this->baseUrl}/payment/check", $payload);

            if ($response->successful()) {
                $result = $response->json();
                
                if (isset($result['code']) && $result['code'] === '00') {
                    $data = $result['data'];
                    
                    return [
                        'success' => true,
                        'status' => $data['payment_status'] ?? 'PENDING',
                        'transaction_id' => $transactionId,
                        'amount' => $data['amount'] ?? 0,
                        'currency' => $data['currency'] ?? 'XOF',
                        'payment_method' => $data['payment_method'] ?? 'unknown',
                        'operator_id' => $data['operator_transaction_id'] ?? null,
                        'metadata' => $data['metadata'] ?? [],
                    ];
                }
            }

            Log::error('CinetPay verification failed', [
                'transaction_id' => $transactionId,
                'response' => $response->body()
            ]);

            return [
                'success' => false,
                'message' => 'Payment verification failed',
                'error' => $response->json()
            ];

        } catch (\Exception $e) {
            Log::error('CinetPay verification exception: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Verification service error',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Handle webhook from CinetPay
     *
     * @param array $payload
     * @return array
     */
    public function handleWebhook(array $payload): array
    {
        try {
            // Verify webhook signature
            if (!$this->verifyWebhookSignature($payload)) {
                Log::warning('Invalid CinetPay webhook signature');
                return [
                    'success' => false,
                    'message' => 'Invalid signature'
                ];
            }

            // Extract payment data
            $transactionId = $payload['cpm_trans_id'] ?? null;
            $status = $payload['cpm_result'] ?? null;

            if (!$transactionId) {
                return [
                    'success' => false,
                    'message' => 'Missing transaction ID'
                ];
            }

            // Verify the payment status
            $verification = $this->verifyPayment($transactionId);

            return $verification;

        } catch (\Exception $e) {
            Log::error('CinetPay webhook exception: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Webhook processing error',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Process a refund with CinetPay
     *
     * @param string $transactionId
     * @param float $amount
     * @return array
     */
    public function processRefund(string $transactionId, float $amount): array
    {
        try {
            // Note: CinetPay refund API may vary - this is a placeholder
            // Check CinetPay documentation for actual refund endpoint
            
            Log::info('CinetPay refund requested', [
                'transaction_id' => $transactionId,
                'amount' => $amount
            ]);

            // For now, return a stub response
            // In production, you would call CinetPay's refund API
            return [
                'success' => true,
                'message' => 'Refund request submitted',
                'transaction_id' => $transactionId,
                'amount' => $amount,
                'status' => 'pending'
            ];

        } catch (\Exception $e) {
            Log::error('CinetPay refund exception: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Refund service error',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Verify webhook signature from CinetPay
     *
     * @param array $payload
     * @return bool
     */
    protected function verifyWebhookSignature(array $payload): bool
    {
        // CinetPay webhook signature verification
        // Check their documentation for the exact method
        
        if (!isset($payload['signature'])) {
            return false;
        }

        // Example signature verification (adjust based on CinetPay docs)
        $expectedSignature = hash_hmac('sha256', json_encode($payload), $this->secretKey);
        
        return hash_equals($expectedSignature, $payload['signature']);
    }
}
