<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            padding: 30px;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        .invoice-box {
            border: 1px solid #eee;
            padding: 30px;
            border-radius: 10px;
        }

        .section {
            margin-top: 20px;
        }

        .info-table, .details-table {
            width: 100%;
            margin-top: 10px;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 6px;
        }

        .details-table th {
            background-color: #f0f0f0;
            padding: 8px;
            border: 1px solid #ddd;
        }

        .details-table td {
            padding: 8px;
            border: 1px solid #ddd;
        }

        .section-title {
            background-color: #efefef;
            padding: 10px;
            font-weight: bold;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .total {
            font-size: 18px;
            font-weight: bold;
            margin-top: 10px;
        }
    </style>
</head>
<body>

    <h1>{{ __('invoice.invoice_title') }}</h1>

    <div class="invoice-box">

        {{-- Transaction Info --}}
        <div class="section">
            <div class="section-title">{{ __('invoice.transaction_info') }}</div>
            <table class="info-table">
                <tr>
                    <td><strong>{{ __('invoice.transaction_id') }}:</strong> {{ $payment->transaction_id }}</td>
                    <td><strong>{{ __('invoice.created_at') }}:</strong> {{ $payment->created_at->format('Y-m-d H:i') }}</td>
                </tr>
                <tr>
                    <td><strong>{{ __('invoice.payer') }}:</strong> {{ $payment->payer?->name ?? '---' }}</td>
                    <td><strong>{{ __('invoice.beneficiary') }}:</strong> {{ $payment->beneficiary?->name ?? '---' }}</td>
                </tr>
            </table>
        </div>

        {{-- Payment Details --}}
        <div class="section">
            <div class="section-title">{{ __('invoice.payment_method') }}</div>
            <table class="details-table">
                <thead>
                    <tr>
                        <th>{{ __('invoice.description') }}</th>
                        <th>{{ __('invoice.type') }}</th>
                        <th>{{ __('invoice.payment_method') }}</th>
                        <th>{{ __('invoice.amount') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $payment->payable_type ?? '-' }}</td>
                        <td>{{ __('invoice.type_' . $payment->type) }}</td>
                        <td>
                            @php
                                $methodKey = 'method_' . $payment->payment_method;
                            @endphp
                            {{ __('invoice.' . $methodKey) }}
                        </td>
                        <td>{{ $payment->amount }} {{ $payment->currency?->code ?? 'SAR' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Total --}}
        <div class="total">
            {{ __('invoice.total') }}: {{ $payment->amount }} {{ $payment->currency?->code ?? 'SAR' }}
        </div>

    </div>

</body>
</html>
