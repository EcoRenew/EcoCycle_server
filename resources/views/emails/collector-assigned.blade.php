<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>EcoCycle | Collector Assigned</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { font-family: Arial, Helvetica, sans-serif; color: #111827; background-color: #f9fafb; }
        .container { max-width: 640px; margin: 0 auto; padding: 16px; }
        .card { border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px; background: #ffffff; }
        .muted { color: #6b7280; }
        .header { text-align: center; margin-bottom: 12px; }
        .brand { color: #059669; }
        .row { display: flex; justify-content: space-between; align-items: center; margin: 6px 0; }
        .divider { border-top: 1px solid #e5e7eb; margin: 12px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2 class="brand">EcoCycle</h2>
            <p class="muted">Collector Assigned to Your Request</p>
        </div>
        <div class="card">
            <p>Hi {{ $request->customer->name ?? 'there' }},</p>
            <p class="muted">A collector has been assigned to your recycling request #{{ $request->request_id ?? '-' }}.</p>

            <div class="row">
                <span><strong>Collector Name:</strong></span>
                <span>{{ $collector->name ?? '-' }}</span>
            </div>
            <div class="row">
                <span><strong>Collector Phone:</strong></span>
                <span>{{ $collector->primary_phone ?? $collector->phone ?? '-' }}</span>
            </div>
            <div class="row">
                <span><strong>Pickup Date:</strong></span>
                <span>{{ optional($request->pickup_date)->format('Y-m-d') ?? (\Carbon\Carbon::parse($request->pickup_date)->format('Y-m-d') ?? '-') }}</span>
            </div>
            <div class="row">
                <span><strong>Status:</strong></span>
                <span>{{ $request->status ?? '-' }}</span>
            </div>

            <div class="divider"></div>
            <p class="muted">Your collector will contact you soon to coordinate the pickup. If you have any urgent questions, reply to this email.</p>
        </div>
        <p class="muted" style="margin-top:12px;">— The EcoCycle Team</p>
    </div>
</body>
</html>
