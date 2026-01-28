<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('emails.review_request_subject', ['service' => $appointment->service?->name ?? 'Szolgáltatás']) }}</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <h1 style="color: #d4af37;">{{ __('emails.booking_confirmation_greeting', ['name' => $appointment->user_name]) }}</h1>
    
    <p>{{ __('emails.review_request_body') }}</p>
    
    <div style="background: #f9f9f9; padding: 20px; border-radius: 8px; margin: 20px 0;">
        <h2 style="margin-top: 0; color: #333;">Szolgáltatás részletei</h2>
        <p><strong>{{ __('booking.service_details') }}:</strong> {{ $appointment->service?->name ?? 'Szolgáltatás' }}</p>
        <p><strong>Dátum:</strong> {{ $appointment->start_time->format('Y.m.d.') }}</p>
        <p><strong>Időpont:</strong> {{ $appointment->start_time->format('H:i') }} - {{ $appointment->end_time->format('H:i') }}</p>
    </div>
    
    <div style="margin: 30px 0; text-align: center;">
        <p style="margin-bottom: 20px; font-size: 16px;"><strong>{{ __('emails.review_request_cta') }}</strong></p>
        
        @if($reviewUrl)
            <a href="{{ $reviewUrl }}" style="display: inline-block; background: #d4af37; color: white; padding: 15px 30px; text-decoration: none; border-radius: 4px; font-size: 16px;">
                Értékelés írása
            </a>
        @endif
    </div>
    
    <p style="font-size: 14px; color: #666; margin-top: 30px;">
        {{ __('emails.review_request_footer') }}
    </p>
    
    <p style="font-size: 12px; color: #666; margin-top: 20px;">
        A link 30 napig érvényes.
    </p>
    
    <hr style="border: none; border-top: 1px solid #eee; margin: 30px 0;">
    
    <p style="font-size: 12px; color: #999;">
        Ez egy automatikus üzenet, kérjük ne válaszoljon rá.
    </p>
</body>
</html>
