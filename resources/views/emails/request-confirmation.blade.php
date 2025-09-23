<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Your Recycling Request is Confirmed! 🌱</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .email-wrapper {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
            color: white;
            padding: 40px 32px;
            text-align: center;
        }
        
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        
        .header .subtitle {
            margin: 8px 0 0 0;
            font-size: 16px;
            opacity: 0.9;
        }
        
        .content {
            padding: 32px;
        }
        
        .success-badge {
            display: inline-flex;
            align-items: center;
            background: #dcfce7;
            color: #166534;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 24px;
        }
        
        .success-badge::before {
            content: "✓";
            margin-right: 8px;
            font-weight: bold;
        }
        
        .request-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 24px;
            margin: 24px 0;
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .detail-row:last-child {
            border-bottom: none;
        }
        
        .detail-label {
            font-weight: 600;
            color: #475569;
            font-size: 14px;
        }
        
        .detail-value {
            font-weight: 500;
            color: #0f172a;
            text-align: right;
            max-width: 60%;
        }
        
        .status {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 16px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }
        
        .status-confirmed {
            background: #dcfce7;
            color: #166534;
        }
        
        .items-section {
            margin-top: 32px;
        }
        
        .items-header {
            font-size: 18px;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
        }
        
        .items-header::before {
            content: "📦";
            margin-right: 8px;
        }
        
        .item {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .item-info {
            flex: 1;
        }
        
        .item-name {
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 4px;
        }
        
        .item-quantity {
            font-size: 14px;
            color: #64748b;
        }
        
        .item-price {
            font-weight: 600;
            color: #059669;
            font-size: 16px;
        }
        
        .next-steps {
            background: #eff6ff;
            border-left: 4px solid #3b82f6;
            padding: 20px;
            margin: 32px 0;
            border-radius: 0 8px 8px 0;
        }
        
        .next-steps h3 {
            margin: 0 0 12px 0;
            color: #1e40af;
            font-size: 16px;
        }
        
        .next-steps p {
            margin: 0;
            color: #475569;
        }
        
        .footer {
            text-align: center;
            padding: 32px;
            background: #f8fafc;
            color: #64748b;
            font-size: 14px;
        }
        
        .contact-info {
            margin-top: 16px;
            padding-top: 16px;
            border-top: 1px solid #e2e8f0;
        }
        
        .eco-tip {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 8px;
            padding: 16px;
            margin: 24px 0;
            text-align: center;
        }
        
        .eco-tip::before {
            content: "🌱";
            font-size: 20px;
            display: block;
            margin-bottom: 8px;
        }
        
        @media (max-width: 640px) {
            body { padding: 10px; }
            .header { padding: 24px 20px; }
            .content { padding: 20px; }
            .detail-row { flex-direction: column; align-items: flex-start; gap: 4px; }
            .detail-value { max-width: 100%; text-align: left; }
            .item { flex-direction: column; align-items: flex-start; gap: 8px; }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="header">
            <h1>Request Confirmed!</h1>
            <p class="subtitle">Thank you for choosing eco-friendly recycling</p>
        </div>
        
        <div class="content">
            <div class="success-badge">
                Your request has been successfully submitted
            </div>
            
            <p>Hi there! 👋</p>
            <p>We've received your recycling request and are excited to help you make a positive environmental impact. Here are the details of your submission:</p>
            
            <div class="request-card">
                <div class="detail-row">
                    <span class="detail-label">Request ID</span>
                    <span class="detail-value">{{ $request->request_id ?? 'REQ-000123' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Service Type</span>
                    <span class="detail-value">{{ $request->request_type ?? 'Standard Pickup' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Scheduled Pickup</span>
                    <span class="detail-value">{{ $request->pickup_date ?? 'March 25, 2025' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Status</span>
                    <span class="detail-value">
                        <span class="status status-confirmed">{{ $request->status ?? 'Confirmed' }}</span>
                    </span>
                </div>
                @if($request->pickupAddress ?? true)
                <div class="detail-row">
                    <span class="detail-label">Pickup Address</span>
                    <span class="detail-value">
                        {{ $request->pickupAddress->street ?? '123 Green Street' }}
                        {{ $request->pickupAddress->city ? ', '.$request->pickupAddress->city : ', Alexandria' }}
                    </span>
                </div>
                @endif
            </div>
            
            <div class="items-section">
                <h3 class="items-header">Your Items</h3>
                @forelse(($request->requestItems ?? [
                    (object)['material' => (object)['material_name' => 'Cardboard'], 'quantity' => 5, 'calculated_price' => '$12.50'],
                    (object)['material' => (object)['material_name' => 'Plastic Bottles'], 'quantity' => 20, 'calculated_price' => '$8.00'],
                    (object)['material' => (object)['material_name' => 'Aluminum Cans'], 'quantity' => 15, 'calculated_price' => '$15.75']
                ]) as $item)
                <div class="item">
                    <div class="item-info">
                        <div class="item-name">{{ $item->material->material_name ?? 'Material' }}</div>
                        <div class="item-quantity">Quantity: {{ $item->quantity ?? '0' }} items</div>
                    </div>
                    <div class="item-price">{{ $item->calculated_price ?? '$0.00' }}</div>
                </div>
                @empty
                <p>No items listed</p>
                @endforelse
            </div>
            
            <div class="next-steps">
                <h3>📅 What happens next?</h3>
                <p>Our team will contact you within 24 hours to confirm the pickup details and provide any additional instructions. Please ensure your items are ready and accessible on the scheduled date.</p>
            </div>
            
            <div class="eco-tip">
                <strong>Eco Tip:</strong> By recycling with us, you're helping reduce waste in landfills and contributing to a sustainable future. Every item counts! 🌍
            </div>
        </div>
        
        <div class="footer">
            <p><strong>Thank you for making a difference!</strong></p>
            <div class="contact-info">
                <p>Questions? Contact us at <strong>support@ecorecycle.com</strong> or call <strong>(555) 123-RECYCLE</strong></p>
                <p>Track your request anytime at <strong>www.ecorecycle.com/track</strong></p>
            </div>
        </div>
    </div>
</body>
</html>