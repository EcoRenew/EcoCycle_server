<!DOCTYPE html>
<html>

<head>
    <title>Payment Successful</title>
</head>

<body>
    <h1>Thank you for your order!</h1>
    <p>Order ID: {{ $order->id }}</p>
    <p>Amount: ${{ $order->amount / 100 }}</p>
    <p>Status: {{ $order->status }}</p>
</body>

</html>
