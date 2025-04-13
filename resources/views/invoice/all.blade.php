<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            padding: 20px;
        }

        .invoice {
            border: 1px solid #ccc;
            padding: 20px;
            margin-bottom: 40px;
            border-radius: 10px;
        }

        .title {
            text-align: center;
            font-size: 20px;
            margin-bottom: 10px;
            font-weight: bold;
        }

        .info-table,
        .details-table {
            width: 100%;
            margin-top: 10px;
            border-collapse: collapse;
        }

        .info-table td,
        .details-table td,
        .details-table th {
            padding: 8px;
            border: 1px solid #ddd;
        }

        .details-table th {
            background-color: #f0f0f0;
        }

        .total {
            font-weight: bold;
            margin-top: 10px;
        }
    </style>
</head>

<body>

    @foreach($transactions as $transaction)
    <div class="invoice">
        <div class="title">{{ __('invoice.invoice_title') }}</div>

        <table class="info-table">
            <tr>
                <td><strong>{{ __('invoice.transaction_id') }}:</strong> {{ $transaction->transaction_id }}</td>
                <td><strong>{{ __('invoice.created_at') }}:</strong> {{ $transaction->created_at->format('Y-m-d H:i') }}</td>
            </tr>
            <tr>
                <td><strong>{{ __('invoice.payer') }}:</strong> {{ $transaction->payer?->name ?? '---' }}</td>
                <td><strong>{{ __('invoice.beneficiary') }}:</strong> {{ $transaction->beneficiary?->name ?? '---' }}</td>
            </tr>
        </table>

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
                    <td>{{ $transaction->payable_type ?? '-' }}</td>
                    <td>{{ __('invoice.type_' . $transaction->type->value) }}</td>
                    <td>
                        @php $methodKey = 'method_' . $transaction->payment_method->value; @endphp
                        {{ __('invoice.' . $methodKey) }}
                    </td>
                    <td>{{ $transaction->amount }} {{ $transaction->currency?->code ?? 'SAR' }}</td>
                </tr>
            </tbody>
        </table>

        <div class="total">
            {{ __('invoice.total') }}: {{ $transaction->amount }} {{ $transaction->currency?->code ?? 'SAR' }}
        </div>
    </div>
    @endforeach

</body>

</html>