<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - {{ $transaction->transaction_code }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            padding: 20px;
        }

        .header {
            border-bottom: 3px solid #667eea;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 5px;
        }

        .company-tagline {
            color: #666;
            font-size: 11px;
        }

        .invoice-title {
            font-size: 28px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }

        .invoice-meta {
            margin-top: 15px;
        }

        .invoice-meta table {
            width: 100%;
        }

        .invoice-meta td {
            padding: 5px 0;
        }

        .invoice-meta .label {
            font-weight: bold;
            width: 150px;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #667eea;
            margin: 30px 0 15px 0;
            padding-bottom: 5px;
            border-bottom: 2px solid #667eea;
        }

        .info-box {
            background: #f8f9fa;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }

        .info-box h3 {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #667eea;
        }

        .info-box p {
            margin: 3px 0;
            font-size: 11px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .items-table thead {
            background: #667eea;
            color: white;
        }

        .items-table th,
        .items-table td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }

        .items-table th {
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
        }

        .items-table tbody tr:nth-child(even) {
            background: #f8f9fa;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .totals-table {
            width: 300px;
            margin-left: auto;
            margin-top: 20px;
        }

        .totals-table td {
            padding: 8px;
        }

        .totals-table .label {
            font-weight: bold;
        }

        .totals-table .total-row {
            border-top: 2px solid #667eea;
            font-size: 14px;
            font-weight: bold;
            background: #f8f9fa;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-paid {
            background: #d4edda;
            color: #155724;
        }

        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 2px solid #ddd;
            text-align: center;
            color: #666;
            font-size: 10px;
        }

        .notes {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
        }

        .notes h4 {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 8px;
            color: #856404;
        }

        .notes p {
            font-size: 10px;
            color: #856404;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div style="display: table; width: 100%;">
            <div style="display: table-cell; width: 50%; vertical-align: top;">
                <div class="company-name">ROCKETEER</div>
                <div class="company-tagline">Voucher Game Store Terpercaya</div>
                <p style="margin-top: 10px; font-size: 10px; color: #666;">
                    Email: support@antigravity.com<br>
                    Phone: +62 123 456 7890
                </p>
            </div>
            <div style="display: table-cell; width: 50%; vertical-align: top; text-align: right;">
                <div class="invoice-title">INVOICE</div>
                <div class="status-badge status-paid">{{ strtoupper($transaction->payment_status) }}</div>
            </div>
        </div>
    </div>

    <!-- Invoice Meta -->
    <div class="invoice-meta">
        <table>
            <tr>
                <td class="label">Invoice Number:</td>
                <td>{{ $transaction->transaction_code }}</td>
                <td class="label" style="text-align: right;">Invoice Date:</td>
                <td style="text-align: right;">{{ $transaction->created_at->format('d F Y') }}</td>
            </tr>
            <tr>
                <td class="label">Payment Date:</td>
                <td>{{ $transaction->paid_at ? $transaction->paid_at->format('d F Y H:i') : '-' }}</td>
                <td class="label" style="text-align: right;">Payment Method:</td>
                <td style="text-align: right;">{{ strtoupper(str_replace('_', ' ', $transaction->payment_method)) }}</td>
            </tr>
        </table>
    </div>

    <!-- Customer Information -->
    <div class="section-title">Customer Information</div>
    <div class="info-box">
        <table style="width: 100%;">
            <tr>
                <td style="width: 50%; vertical-align: top;">
                    <h3>Bill To:</h3>
                    <p><strong>{{ $transaction->user->name }}</strong></p>
                    <p>Email: {{ $transaction->user->email }}</p>
                    @if($transaction->user->phone)
                        <p>Phone: {{ $transaction->user->phone }}</p>
                    @endif
                </td>
                <td style="width: 50%; vertical-align: top;">
                    @if($transaction->game_user_id || $transaction->game_server)
                        <h3>Game Account:</h3>
                        @if($transaction->game_user_id)
                            <p>User ID: {{ $transaction->game_user_id }}</p>
                        @endif
                        @if($transaction->game_server)
                            <p>Server: {{ $transaction->game_server }}</p>
                        @endif
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <!-- Transaction Items -->
    <div class="section-title">Transaction Details</div>
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 50%;">Product Description</th>
                <th class="text-center" style="width: 15%;">Quantity</th>
                <th class="text-right" style="width: 20%;">Unit Price</th>
                <th class="text-right" style="width: 15%;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong>{{ $transaction->product->name }}</strong><br>
                    <small style="color: #666;">{{ $transaction->product->category->name }}</small>
                </td>
                <td class="text-center">{{ $transaction->quantity }}</td>
                <td class="text-right">Rp {{ number_format($transaction->product->price, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($transaction->product->price * $transaction->quantity, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Totals -->
    <table class="totals-table">
        <tr>
            <td class="label">Subtotal:</td>
            <td class="text-right">Rp {{ number_format($transaction->product->price * $transaction->quantity, 0, ',', '.') }}</td>
        </tr>
        @if($transaction->discount_amount > 0)
            <tr>
                <td class="label">Discount:</td>
                <td class="text-right" style="color: #28a745;">- Rp {{ number_format($transaction->discount_amount, 0, ',', '.') }}</td>
            </tr>
            @if($transaction->voucherCode)
                <tr>
                    <td colspan="2" style="font-size: 10px; color: #666; padding-top: 0;">
                        Voucher: {{ $transaction->voucherCode->code }}
                    </td>
                </tr>
            @endif
        @endif
        <tr class="total-row">
            <td class="label">Total Paid:</td>
            <td class="text-right">Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</td>
        </tr>
    </table>

    <!-- Notes -->
    <div class="notes">
        <h4>Important Information:</h4>
        <p>
            ✓ This is a computer-generated invoice and is valid without signature.<br>
            ✓ Please keep this invoice for your records.<br>
            ✓ For any inquiries, please contact our customer support with your invoice number.
        </p>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Thank you for your purchase!</p>
        <p>© {{ date('Y') }} ROCKETEER Voucher Store. All rights reserved.</p>
        <p>Generated on {{ now()->format('d F Y H:i:s') }} WIB</p>
    </div>
</body>
</html>
