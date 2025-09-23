<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #111827; font-size: 12px; }
        .header { text-align: center; margin-bottom: 10px; }
        .brand { color: #059669; }
        .muted { color: #6b7280; }
        .row { display: flex; justify-content: space-between; margin: 4px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #e5e7eb; padding: 6px; text-align: left; }
        th { background: #f3f4f6; }
        .right { text-align: right; }
        .total { font-weight: 700; }
    </style>
</head>
<body>
    <div class="header">
        <h2 class="brand">EcoCycle</h2>
        <div class="muted">Recycling Request Invoice</div>
    </div>

    <div class="row"><div>Invoice ID: {{ $invoice->invoice_id ?? '-' }}</div><div>Date: {{ optional($invoice->invoice_date)->format('Y-m-d') ?? (\Carbon\Carbon::parse($invoice->invoice_date)->format('Y-m-d') ?? '-') }}</div></div>
    <div class="row"><div>Request ID: {{ $request->request_id ?? '-' }}</div><div>Customer: {{ $request->customer->name ?? '-' }}</div></div>

    <table>
        <thead>
            <tr>
                <th>Material</th>
                <th class="right">Quantity</th>
                <th class="right">Price</th>
            </tr>
        </thead>
        <tbody>
            @php $total = 0; @endphp
            @foreach(($request->requestItems ?? []) as $item)
                @php $price = (float) ($item->calculated_price ?? 0); $total += $price; @endphp
                <tr>
                    <td>{{ $item->material->material_name ?? 'Material' }}</td>
                    <td class="right">{{ $item->quantity }}</td>
                    <td class="right">{{ number_format($price, 2) }}</td>
                </tr>
            @endforeach
            <tr>
                <td class="total" colspan="2">Total</td>
                <td class="right total">{{ number_format($total, 2) }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
