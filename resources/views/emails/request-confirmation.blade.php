<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Recycling Request Confirmation</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { font-family: Arial, Helvetica, sans-serif; color: #111827; }
        .container { max-width: 640px; margin: 0 auto; padding: 16px; }
        .card { border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px; }
        .muted { color: #6b7280; }
    </style>
  </head>
  <body>
    <div class="container">
      <h2>Thank you for your request!</h2>
      <p class="muted">Your recycling request has been received.</p>
      <div class="card">
        <p><strong>Request ID:</strong> {{ $request->request_id ?? '-' }}</p>
        <p><strong>Type:</strong> {{ $request->request_type ?? '-' }}</p>
        <p><strong>Pickup Date:</strong> {{ $request->pickup_date ?? '-' }}</p>
        <p><strong>Status:</strong> {{ $request->status ?? '-' }}</p>
        @if($request->pickupAddress)
          <p><strong>Pickup Address:</strong> {{ $request->pickupAddress->street ?? '' }} {{ $request->pickupAddress->city ? ', '.$request->pickupAddress->city : '' }}</p>
        @endif
        <h3>Items</h3>
        <ul>
          @foreach(($request->requestItems ?? []) as $item)
            <li>{{ $item->material->material_name ?? 'Material' }} — Qty: {{ $item->quantity }} — Price: {{ $item->calculated_price }}</li>
          @endforeach
        </ul>
      </div>
      <p class="muted">We will contact you soon with further details.</p>
    </div>
  </body>
 </html>

