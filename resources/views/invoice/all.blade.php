<!-- resources/views/pdf/transactions.blade.php -->
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            direction: rtl;
        }

        .transaction {
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 8px;
        }

        .green {
            color: green;
        }

        .red {
            color: red;
        }

        .title {
            font-weight: bold;
        }
    </style>
</head>

<body>

    <h2>المعاملات</h2>

    @foreach($transactions as $t)
    <div class="transaction">
        <div class="title">{{ $t['amount'] }} {{ $t['currency'] }} - <span class="{{ $t['status_class'] }}">{{ $t['status'] }}</span></div>
        <div>{{ $t['name'] }}</div>
        @if(!empty($t['desc']))
        <div>{{ $t['desc'] }}</div>
        @endif
        <div>{{ $t['date'] }} - {{ $t['time'] }}</div>
    </div>
    @endforeach

</body>

</html>