<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>EcoCycle | Request Completed</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { font-family: Arial, Helvetica, sans-serif; color: #111827; background-color: #f9fafb; }
        .container { max-width: 640px; margin: 0 auto; padding: 16px; }
        .card { border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px; background: #ffffff; }
        .muted { color: #6b7280; }
        .header { text-align: center; margin-bottom: 12px; }
        .brand { color: #059669; }
        .row { display: flex; justify-content: space-between; align-items: center; margin: 6px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #e5e7eb; padding: 8px; text-align: left; }
        th { background: #f3f4f6; }
        .right { text-align: right; }
        .total { font-weight: 700; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2 class="brand">EcoCycle</h2>
            <p class="muted">Your Recycling Request Is Completed</p>
        </div>
        <div class="card">
            <p>Hi {{ $request->customer->name ?? 'there' }},</p>
            <p class="muted">Your recycling request #{{ $request->request_id ?? '-' }} has been marked as completed. Here is your invoice summary.</p>
            <div class="row">
                <span><strong>Invoice ID:</strong></span>
                <span>{{ $invoice->invoice_id ?? '-' }}</span>
            </div>
            <div class="row">
                <span><strong>Invoice Date:</strong></span>
                <span>{{ optional($invoice->invoice_date)->format('Y-m-d') ?? (\Carbon\Carbon::parse($invoice->invoice_date)->format('Y-m-d') ?? '-') }}</span>
            </div>
            <div class="row">
                <span><strong>Pickup Date:</strong></span>
                <span>{{ optional($request->pickup_date)->format('Y-m-d') ?? (\Carbon\Carbon::parse($request->pickup_date)->format('Y-m-d') ?? '-') }}</span>
            </div>

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
        </div>
        <p class="muted" style="margin-top:12px;">Thank you for recycling with EcoCycle!</p>
    </div>
</body>
</html>
