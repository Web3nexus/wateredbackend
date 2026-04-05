<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Transaction History</title>
    <style>
        body { font-family: sans-serif; font-size: 10pt; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2>Transaction History</h2>
    <p>Exported on {{ now()->format('F d, Y') }}</p>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Source</th>
                <th>Description</th>
                <th>Reference</th>
                <th>Customer</th>
                <th>Amount</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $tx)
            <tr>
                <td>{{ $tx->created_at->format('Y-m-d H:i') }}</td>
                <td>{{ $tx->source_type }}</td>
                <td>{{ $tx->description }}</td>
                <td>{{ $tx->reference }}</td>
                <td>{{ $tx->customer_name }}</td>
                <td>{{ number_format($tx->amount, 2) }}</td>
                <td>{{ ucfirst($tx->payment_status) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
