<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Transaction Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.6;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #9333ea;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #9333ea;
            margin: 0;
            font-size: 24px;
        }
        .header p {
            color: #666;
            margin: 5px 0;
        }
        .summary {
            margin: 20px 0;
            background-color: #f9fafb;
            padding: 15px;
            border-radius: 8px;
        }
        .summary h3 {
            margin: 0 0 10px;
            color: #1f2937;
            font-size: 16px;
        }
        .summary-grid {
            display: table;
            width: 100%;
        }
        .summary-item {
            display: table-row;
        }
        .summary-label {
            display: table-cell;
            padding: 5px 10px;
            color: #6b7280;
            font-weight: bold;
        }
        .summary-value {
            display: table-cell;
            padding: 5px 10px;
            color: #1f2937;
            text-align: right;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background-color: #9333ea;
            color: white;
            padding: 12px 8px;
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
        }
        td {
            border-bottom: 1px solid #e5e7eb;
            padding: 10px 8px;
            color: #1f2937;
        }
        tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .status {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-paid {
            background-color: #d1fae5;
            color: #065f46;
        }
        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }
        .status-failed {
            background-color: #fee2e2;
            color: #991b1b;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            color: #9ca3af;
            font-size: 10px;
            border-top: 1px solid #e5e7eb;
            padding-top: 15px;
        }
        .total-row {
            font-weight: bold;
            background-color: #f3e8ff !important;
            border-top: 2px solid #9333ea;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>🚀 ROCKETEER</h1>
        <h2 style="margin: 10px 0; color: #1f2937;">Transaction Report</h2>
        <p>Generated: {{ date('d F Y H:i') }} WIB</p>
        @if($dateFrom || $dateTo)
        <p>Period: 
            {{ $dateFrom ? \Carbon\Carbon::parse($dateFrom)->format('d M Y') : 'Start' }} 
            - 
            {{ $dateTo ? \Carbon\Carbon::parse($dateTo)->format('d M Y') : 'End' }}
        </p>
        @endif
        @if($status)
        <p>Status Filter: <strong>{{ ucfirst($status) }}</strong></p>
        @endif
    </div>

    <!-- Summary -->
    <div class="summary">
        <h3>📊 Report Summary</h3>
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-label">Total Transactions:</div>
                <div class="summary-value">{{ $transactions->count() }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Paid Transactions:</div>
                <div class="summary-value">{{ $transactions->where('payment_status', 'paid')->count() }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Pending Transactions:</div>
                <div class="summary-value">{{ $transactions->where('payment_status', 'pending')->count() }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Failed Transactions:</div>
                <div class="summary-value">{{ $transactions->where('payment_status', 'failed')->count() }}</div>
            </div>
            <div class="summary-item" style="border-top: 2px solid #9333ea; margin-top: 5px;">
                <div class="summary-label" style="color: #9333ea; font-size: 14px;">Total Revenue:</div>
                <div class="summary-value" style="color: #9333ea; font-size: 16px; font-weight: bold;">
                    Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Transactions Table -->
    <table>
        <thead>
            <tr>
                <th style="width: 15%;">Code</th>
                <th style="width: 18%;">User</th>
                <th style="width: 20%;">Product</th>
                <th style="width: 8%;">Qty</th>
                <th style="width: 17%;">Total</th>
                <th style="width: 12%;">Status</th>
                <th style="width: 10%;">Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $t)
            <tr>
                <td style="font-family: monospace; font-size: 10px;">{{ $t->transaction_code }}</td>
                <td>
                    <strong>{{ $t->user->name }}</strong><br>
                    <span style="font-size: 10px; color: #6b7280;">{{ $t->user->email }}</span>
                </td>
                <td>{{ $t->product->name }}</td>
                <td style="text-align: center;">{{ $t->quantity }}</td>
                <td style="font-weight: bold;">Rp {{ number_format($t->total_price, 0, ',', '.') }}</td>
                <td>
                    @if($t->payment_status === 'paid')
                        <span class="status status-paid">Paid</span>
                    @elseif($t->payment_status === 'pending')
                        <span class="status status-pending">Pending</span>
                    @else
                        <span class="status status-failed">{{ ucfirst($t->payment_status) }}</span>
                    @endif
                </td>
                <td style="font-size: 10px;">{{ $t->created_at->format('d/m/Y') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; padding: 30px; color: #9ca3af;">
                    No transactions found
                </td>
            </tr>
            @endforelse
            
            @if($transactions->count() > 0)
            <tr class="total-row">
                <td colspan="4" style="text-align: right; font-size: 14px;">TOTAL REVENUE:</td>
                <td colspan="3" style="font-size: 16px; color: #9333ea;">
                    Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                </td>
            </tr>
            @endif
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <p><strong>ROCKETEER</strong> - Voucher Game Terlengkap dengan Harga Terbaik</p>
        <p>This is an automatically generated report. For questions, please contact support.</p>
        <p>&copy; {{ date('Y') }} ROCKETEER. All rights reserved.</p>
    </div>
</body>
</html>