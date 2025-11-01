<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\{Invoice, InvoiceLine, Student, School, FeeType, Refund};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    /**
     * Display a listing of invoices
     */
    public function index(Request $request)
    {
        $query = Invoice::with(['student', 'school', 'invoiceLines.feeType']);

        if ($request->has('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('school_id')) {
            $query->where('school_id', $request->school_id);
        }

        $invoices = $query->latest()->paginate(20);

        return response()->json($invoices);
    }

    /**
     * Store a newly created invoice
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'school_id' => 'required|exists:schools,id',
            'student_id' => 'required|exists:students,id',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'currency' => 'nullable|string|size:3',
            'notes' => 'nullable|string',
            'lines' => 'required|array|min:1',
            'lines.*.fee_type_id' => 'nullable|exists:fee_types,id',
            'lines.*.description' => 'required|string',
            'lines.*.quantity' => 'required|integer|min:1',
            'lines.*.unit_price' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Generate invoice number
            $invoiceNumber = 'INV-' . date('Y') . '-' . str_pad(Invoice::count() + 1, 5, '0', STR_PAD_LEFT);

            // Calculate totals
            $subtotal = 0;
            foreach ($validated['lines'] as $line) {
                $subtotal += $line['quantity'] * $line['unit_price'];
            }

            $invoice = Invoice::create([
                'school_id' => $validated['school_id'],
                'student_id' => $validated['student_id'],
                'invoice_number' => $invoiceNumber,
                'issue_date' => $validated['issue_date'],
                'due_date' => $validated['due_date'],
                'subtotal' => $subtotal,
                'tax_amount' => 0,
                'total_amount' => $subtotal,
                'paid_amount' => 0,
                'currency' => $validated['currency'] ?? 'USD',
                'status' => 'pending',
                'notes' => $validated['notes'] ?? null,
            ]);

            // Create invoice lines
            foreach ($validated['lines'] as $line) {
                InvoiceLine::create([
                    'invoice_id' => $invoice->id,
                    'fee_type_id' => $line['fee_type_id'] ?? null,
                    'description' => $line['description'],
                    'quantity' => $line['quantity'],
                    'unit_price' => $line['unit_price'],
                    'amount' => $line['quantity'] * $line['unit_price'],
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'invoice' => $invoice->load(['invoiceLines', 'student', 'school'])
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create invoice',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified invoice
     */
    public function show(Invoice $invoice)
    {
        return response()->json($invoice->load(['student', 'school', 'invoiceLines.feeType', 'payments']));
    }

    /**
     * Update the specified invoice
     */
    public function update(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'due_date' => 'nullable|date',
            'status' => 'nullable|in:draft,pending,paid,partial,overdue,cancelled',
            'notes' => 'nullable|string',
        ]);

        $invoice->update($validated);

        return response()->json([
            'success' => true,
            'invoice' => $invoice->fresh(['student', 'school', 'invoiceLines'])
        ]);
    }

    /**
     * Remove the specified invoice
     */
    public function destroy(Invoice $invoice)
    {
        if ($invoice->paid_amount > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete invoice with payments'
            ], 422);
        }

        $invoice->delete();

        return response()->json([
            'success' => true,
            'message' => 'Invoice deleted successfully'
        ]);
    }

    /**
     * Download invoice as PDF
     */
    public function downloadPdf(Invoice $invoice)
    {
        $invoice->load(['student', 'school', 'invoiceLines.feeType']);

        $pdf = Pdf::loadView('invoices.pdf', [
            'invoice' => $invoice
        ]);

        return $pdf->download("invoice-{$invoice->invoice_number}.pdf");
    }

    /**
     * Send invoice via email
     */
    public function sendEmail(Invoice $invoice)
    {
        // TODO: Implement email sending with Mailable
        // For now, return success message
        return response()->json([
            'success' => true,
            'message' => 'Invoice email functionality will be implemented',
            'invoice' => $invoice
        ]);
    }

    /**
     * Request refund for invoice
     */
    public function requestRefund(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0|max:' . $invoice->paid_amount,
            'reason' => 'required|string',
        ]);

        if ($invoice->paid_amount <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'No payments to refund'
            ], 422);
        }

        // Get the latest completed payment
        $payment = $invoice->payments()->where('status', 'completed')->latest()->first();

        if (!$payment) {
            return response()->json([
                'success' => false,
                'message' => 'No completed payment found'
            ], 422);
        }

        $refund = Refund::create([
            'payment_id' => $payment->id,
            'invoice_id' => $invoice->id,
            'refund_reference' => 'REF-' . date('Ymd') . '-' . strtoupper(Str::random(8)),
            'amount' => $validated['amount'],
            'currency' => $invoice->currency,
            'status' => 'requested',
            'reason' => $validated['reason'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Refund requested successfully',
            'refund' => $refund
        ], 201);
    }
}
