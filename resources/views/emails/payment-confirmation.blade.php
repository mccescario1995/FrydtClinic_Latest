<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payment Confirmation - FRYDT Clinic</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #007bff; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background-color: #f8f9fa; }
        .payment-details { background-color: white; padding: 15px; margin: 20px 0; border-radius: 5px; }
        .footer { text-align: center; padding: 20px; font-size: 12px; color: #666; }
        .highlight { color: #007bff; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>FRYDT Clinic</h1>
            <h2>Payment Confirmation</h2>
        </div>

        <div class="content">
            <p>Dear {{ $patient->name }},</p>

            <p>We are pleased to confirm that your payment has been successfully processed and approved.</p>

            <div class="payment-details">
                <h3>Payment Details:</h3>
                <p><strong>Payment Reference:</strong> {{ $payment->payment_reference }}</p>
                <p><strong>Amount:</strong> ₱{{ number_format($payment->amount, 2) }}</p>
                <p><strong>Payment Method:</strong> {{ ucfirst($payment->payment_method) }}</p>
                <p><strong>Status:</strong> <span class="highlight">{{ ucfirst($payment->status) }}</span></p>
                @if($payment->gcash_reference)
                <p><strong>GCash Reference:</strong> {{ $payment->gcash_reference }}</p>
                @endif
                <p><strong>Payment Date:</strong> {{ $payment->paid_at ? $payment->paid_at->format('M d, Y h:i A') : 'N/A' }}</p>
            </div>

            <p>Thank you for choosing FRYDT Clinic. Your payment has been confirmed and your services are now ready.</p>

            <p>If you have any questions about your payment or services, please don't hesitate to contact us.</p>

            <p>Best regards,<br>
            FRYDT Clinic Team</p>
        </div>

        <div class="footer">
            <p>This is an automated message. Please do not reply to this email.</p>
            <p>© {{ date('Y') }} FRYDT Clinic. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
