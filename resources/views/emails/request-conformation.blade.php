<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Recycling Request Confirmation</title>
</head>
<body>
    <h2>Hello {{ $request->customer->name }},</h2>

    <p>Thank you for submitting your recycling request! 🎉</p>

    <p><strong>Request Type:</strong> {{ $request->request_type }}</p>
    <p><strong>Pickup Date:</strong> {{ $request->pickup_date }}</p>
    <p><strong>Pickup Address:</strong> {{ $request->pickupAddress->full_address }}</p>

    <h3>Materials:</h3>
    <ul>
        @foreach($request->requestItems as $item)
            <li>{{ $item->material->name }} — Quantity: {{ $item->quantity }} (Value: {{ $item->calculated_price }})</li>
        @endforeach
    </ul>

    <p>We’ll notify you once a collector is assigned.</p>

    <br>
    <p>Thanks, <br> The Recycling Team</p>
</body>
</html>
