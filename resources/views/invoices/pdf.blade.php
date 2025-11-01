<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #3490dc;
        }
        .school-info {
            text-align: center;
            margin-bottom: 10px;
        }
        .school-name {
            font-size: 24px;
            font-weight: bold;
            color: #3490dc;
        }
        .invoice-title {
            font-size: 18px;
            font-weight: bold;
            margin-top: 10px;
        }
        .info-section {
            margin-bottom: 20px;
        }
        .info-row {
            margin-bottom: 5px;
        }
        .label {
            font-weight: bold;
            display: inline-block;
            width: 150px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 10px;
            text-align: left;
            font-weight: bold;
        }
        td {
            border: 1px solid #dee2e6;
            padding: 10px;
        }
        .text-right {
            text-align: right;
        }
        .totals {
            margin-top: 20px;
            float: right;
            width: 300px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
        }
        .total-label {
            font-weight: bold;
        }
        .grand-total {
            border-top: 2px solid #333;
            padding-top: 10px;
            margin-top: 10px;
            font-size: 16px;
            font-weight: bold;
        }
        .footer {
            margin-top: 80px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 11px;
        }
        .status-pending { background-color: #ffc107; color: #000; }
        .status-paid { background-color: #28a745; color: #fff; }
        .status-partial { background-color: #17a2b8; color: #fff; }
        .status-overdue { background-color: #dc3545; color: #fff; }
    </style>
</head>
<body>
    <div class="header">
        <div class="school-info">
            <div class="school-name">{{ $invoice->school->name }}</div>
            <div>{{ $invoice->school->address }}</div>
            <div>{{ $invoice->school->phone }} | {{ $invoice->school->email }}</div>
        </div>
        <div class="invoice-title">INVOICE</div>
    </div>

    <div class="info-section">
        <div class="info-row">
            <span class="label">Invoice Number:</span>
            <span>{{ $invoice->invoice_number }}</span>
        </div>
        <div class="info-row">
            <span class="label">Issue Date:</span>
            <span>{{ $invoice->issue_date->format('F d, Y') }}</span>
        </div>
        <div class="info-row">
            <span class="label">Due Date:</span>
            <span>{{ $invoice->due_date->format('F d, Y') }}</span>
        </div>
        <div class="info-row">
            <span class="label">Status:</span>
            <span class="status-badge status-{{ $invoice->status }}">{{ strtoupper($invoice->status) }}</span>
        </div>
    </div>

    <div class="info-section">
        <strong>Bill To:</strong>
        <div>{{ $invoice->student->first_name }} {{ $invoice->student->last_name }}</div>
        <div>Registration: {{ $invoice->student->registration_number }}</div>
        @if($invoice->student->parent_name)
        <div>Parent/Guardian: {{ $invoice->student->parent_name }}</div>
        @endif
        @if($invoice->student->parent_email)
        <div>Email: {{ $invoice->student->parent_email }}</div>
        @endif
        @if($invoice->student->parent_phone)
        <div>Phone: {{ $invoice->student->parent_phone }}</div>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th class="text-right">Quantity</th>
                <th class="text-right">Unit Price</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->invoiceLines as $line)
            <tr>
                <td>{{ $line->description }}</td>
                <td class="text-right">{{ $line->quantity }}</td>
                <td class="text-right">{{ number_format($line->unit_price, 2) }} {{ $invoice->currency }}</td>
                <td class="text-right">{{ number_format($line->amount, 2) }} {{ $invoice->currency }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <div class="total-row">
            <span class="total-label">Subtotal:</span>
            <span>{{ number_format($invoice->subtotal, 2) }} {{ $invoice->currency }}</span>
        </div>
        @if($invoice->tax_amount > 0)
        <div class="total-row">
            <span class="total-label">Tax:</span>
            <span>{{ number_format($invoice->tax_amount, 2) }} {{ $invoice->currency }}</span>
        </div>
        @endif
        <div class="total-row grand-total">
            <span class="total-label">Total:</span>
            <span>{{ number_format($invoice->total_amount, 2) }} {{ $invoice->currency }}</span>
        </div>
        @if($invoice->paid_amount > 0)
        <div class="total-row">
            <span class="total-label">Paid:</span>
            <span>{{ number_format($invoice->paid_amount, 2) }} {{ $invoice->currency }}</span>
        </div>
        <div class="total-row">
            <span class="total-label">Balance Due:</span>
            <span>{{ number_format($invoice->total_amount - $invoice->paid_amount, 2) }} {{ $invoice->currency }}</span>
        </div>
        @endif
    </div>

    <div style="clear: both;"></div>

    @if($invoice->notes)
    <div class="info-section" style="margin-top: 40px;">
        <strong>Notes:</strong>
        <div>{{ $invoice->notes }}</div>
    </div>
    @endif

    <div class="footer">
        <p>Thank you for your payment!</p>
        <p>This is a computer-generated invoice and does not require a signature.</p>
    </div>
</body>
</html>
