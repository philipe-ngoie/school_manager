<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\{Payment, Invoice, Transaction};
use App\Services\Payment\CinetPayService;
use App\Services\Payment\StripeAdapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    /**
     * Display a listing of payments
     */
    public function index(Request $request)
    {
        $query = Payment::with(['invoice', 'student']);

        if ($request->has('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $payments = $query->latest()->paginate(20);

        return response()->json($payments);
    }

    /**
     * Display the specified payment
     */
    public function show(Payment $payment)
    {
        return response()->json($payment->load(['invoice', 'student', 'transactions']));
    }

    /**
     * Initiate a new payment
     */
    public function initiatePayment(Request $request)
    {
        $validated = $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'payment_method' => 'required|in:cinetpay,stripe,cash,bank_transfer',
            'amount' => 'required|numeric|min:0',
        ]);

        try {
            $invoice = Invoice::with('student')->findOrFail($validated['invoice_id']);

            // Create payment record
            $payment = Payment::create([
                'invoice_id' => $invoice->id,
                'student_id' => $invoice->student_id,
                'payment_reference' => 'PAY-' . date('Ymd') . '-' . strtoupper(Str::random(8)),
                'payment_method' => $validated['payment_method'],
                'amount' => $validated['amount'],
                'currency' => $invoice->currency,
                'status' => 'pending',
            ]);

            // If using online payment gateway, create payment session
            if (in_array($validated['payment_method'], ['cinetpay', 'stripe'])) {
                $gateway = $validated['payment_method'] === 'cinetpay' 
                    ? new CinetPayService() 
                    : new StripeAdapter();

                $paymentData = [
                    'transaction_id' => $payment->payment_reference,
                    'amount' => $payment->amount,
                    'currency' => $payment->currency,
                    'description' => "Payment for invoice {$invoice->invoice_number}",
                    'customer_name' => $invoice->student->first_name,
                    'customer_surname' => $invoice->student->last_name,
                    'customer_email' => $invoice->student->parent_email ?? 'noemail@example.com',
                    'customer_phone' => $invoice->student->parent_phone ?? '',
                ];

                $result = $gateway->createPaymentSession($paymentData);

                if ($result['success']) {
                    $payment->update([
                        'provider_payment_id' => $result['payment_token'] ?? $result['session_id'] ?? null,
                    ]);

                    return response()->json([
                        'success' => true,
                        'payment' => $payment,
                        'payment_url' => $result['payment_url'],
                    ]);
                } else {
                    $payment->update(['status' => 'failed']);
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to create payment session',
                        'error' => $result['message'] ?? 'Unknown error'
                    ], 422);
                }
            }

            // For manual payments (cash, bank_transfer)
            return response()->json([
                'success' => true,
                'payment' => $payment,
                'message' => 'Payment initiated. Please complete the payment manually.'
            ]);

        } catch (\Exception $e) {
            Log::error('Payment initiation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to initiate payment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify a payment
     */
    public function verifyPayment(Request $request)
    {
        $validated = $request->validate([
            'payment_reference' => 'required|string',
        ]);

        try {
            $payment = Payment::where('payment_reference', $validated['payment_reference'])->firstOrFail();

            if ($payment->status === 'completed') {
                return response()->json([
                    'success' => true,
                    'message' => 'Payment already completed',
                    'payment' => $payment
                ]);
            }

            // Verify with payment gateway
            $gateway = $payment->payment_method === 'cinetpay' 
                ? new CinetPayService() 
                : new StripeAdapter();

            $result = $gateway->verifyPayment($payment->payment_reference);

            if ($result['success'] && strtoupper($result['status']) === 'COMPLETED') {
                DB::transaction(function () use ($payment, $result) {
                    // Update payment
                    $payment->update([
                        'status' => 'completed',
                        'paid_at' => now(),
                        'provider_response' => json_encode($result),
                    ]);

                    // Update invoice
                    $invoice = $payment->invoice;
                    $newPaidAmount = $invoice->paid_amount + $payment->amount;
                    $invoice->update([
                        'paid_amount' => $newPaidAmount,
                        'status' => $newPaidAmount >= $invoice->total_amount ? 'paid' : 'partial'
                    ]);

                    // Create transaction
                    Transaction::create([
                        'payment_id' => $payment->id,
                        'school_id' => $invoice->school_id,
                        'transaction_id' => 'TXN-' . strtoupper(Str::random(16)),
                        'type' => 'payment',
                        'amount' => $payment->amount,
                        'currency' => $payment->currency,
                        'description' => "Payment for invoice {$invoice->invoice_number}",
                        'transaction_date' => now(),
                    ]);
                });

                return response()->json([
                    'success' => true,
                    'message' => 'Payment verified and completed',
                    'payment' => $payment->fresh()
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Payment not completed',
                'status' => $result['status'] ?? 'unknown'
            ]);

        } catch (\Exception $e) {
            Log::error('Payment verification failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to verify payment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle CinetPay webhook
     */
    public function webhookCinetPay(Request $request)
    {
        try {
            $gateway = new CinetPayService();
            $result = $gateway->handleWebhook($request->all());

            if ($result['success']) {
                $transactionId = $result['transaction_id'] ?? $request->input('cpm_trans_id');
                
                $payment = Payment::where('payment_reference', $transactionId)->first();
                
                if ($payment && $payment->status !== 'completed') {
                    // Verify and update payment
                    $this->verifyPayment(new Request(['payment_reference' => $transactionId]));
                }

                return response()->json(['status' => 'success']);
            }

            return response()->json(['status' => 'failed'], 400);

        } catch (\Exception $e) {
            Log::error('CinetPay webhook error: ' . $e->getMessage());
            return response()->json(['status' => 'error'], 500);
        }
    }

    /**
     * Handle Stripe webhook
     */
    public function webhookStripe(Request $request)
    {
        try {
            $gateway = new StripeAdapter();
            $result = $gateway->handleWebhook($request->all());

            Log::info('Stripe webhook received', $result);

            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            Log::error('Stripe webhook error: ' . $e->getMessage());
            return response()->json(['status' => 'error'], 500);
        }
    }
}
