<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('emails.cancellation_confirmation_subject') }}</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <h1 style="color: #d4af37;">{{ __('emails.booking_confirmation_greeting', ['name' => $appointment->user_name]) }}</h1>
    
    <p>{{ __('emails.cancellation_confirmation_body') }}</p>
    
    <div style="background: #f9f9f9; padding: 20px; border-radius: 8px; margin: 20px 0;">
        <h2 style="margin-top: 0; color: #333;">Lemondott időpont részletei</h2>
        <p><strong>{{ __('booking.service_details') }}:</strong> {{ $appointment->service?->name ?? 'Szolgáltatás' }}</p>
        <p><strong>Eredeti dátum:</strong> {{ $appointment->start_time->format('Y.m.d.') }}</p>
        <p><strong>Eredeti időpont:</strong> {{ $appointment->start_time->format('H:i') }} - {{ $appointment->end_time->format('H:i') }}</p>
        <p><strong>Lemondás ideje:</strong> {{ $cancelledAt->format('Y.m.d. H:i') }}</p>
    </div>
    
    <div style="margin: 30px 0;">
        <p style="margin-bottom: 15px;"><strong>Új időpont foglalása:</strong></p>
        
        <a href="{{ $rebookUrl }}" style="display: inline-block; background: #d4af37; color: white; padding: 12px 24px; text-decoration: none; border-radius: 4px;">
            Új időpont foglalása
        </a>
    </div>
    
    <p>{{ __('emails.cancellation_confirmation_footer') }}</p>
    
    <hr style="border: none; border-top: 1px solid #eee; margin: 30px 0;">
    
    <p style="font-size: 12px; color: #999;">
        Ez egy automatikus üzenet, kérjük ne válaszoljon rá.
    </p>
</body>
</html>
