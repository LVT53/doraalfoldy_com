<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('emails.booking_confirmation_subject', ['service' => $appointment->service?->name ?? 'Szolgáltatás']) }}</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <h1 style="color: #d4af37;">{{ __('emails.booking_confirmation_greeting', ['name' => $appointment->user_name]) }}</h1>
    
    <p>{{ __('emails.booking_confirmation_body') }}</p>
    
    <div style="background: #f9f9f9; padding: 20px; border-radius: 8px; margin: 20px 0;">
        <h2 style="margin-top: 0; color: #333;">{{ __('booking.appointment_details') }}</h2>
        <p><strong>{{ __('booking.service_details') }}:</strong> {{ $appointment->service?->name ?? 'Szolgáltatás' }}</p>
        <p><strong>{{ __('booking.duration') }}:</strong> {{ $appointment->service?->duration_minutes ?? 0 }} perc</p>
        <p><strong>{{ __('booking.price') }}:</strong> {{ number_format($appointment->price_at_booking, 0, ',', ' ') }} Ft</p>
        <p><strong>Dátum:</strong> {{ $appointment->start_time->format('Y.m.d.') }}</p>
        <p><strong>Időpont:</strong> {{ $appointment->start_time->format('H:i') }} - {{ $appointment->end_time->format('H:i') }}</p>
        @if($appointment->user_phone)
            <p><strong>Telefon:</strong> {{ $appointment->user_phone }}</p>
        @endif
    </div>
    
    <div style="margin: 30px 0;">
        <p style="margin-bottom: 15px;"><strong>Időpont kezelése:</strong></p>
        
        @if($cancelUrl)
            <a href="{{ $cancelUrl }}" style="display: inline-block; background: #dc3545; color: white; padding: 12px 24px; text-decoration: none; border-radius: 4px; margin-right: 10px;">
                Lemondás
            </a>
        @endif
        
        @if($rescheduleUrl)
            <a href="{{ $rescheduleUrl }}" style="display: inline-block; background: #d4af37; color: white; padding: 12px 24px; text-decoration: none; border-radius: 4px;">
                Átütemezés
            </a>
        @endif
    </div>
    
    <p style="font-size: 12px; color: #666; margin-top: 30px;">
        A linkek 7 napig érvényesek.
    </p>
    
    <hr style="border: none; border-top: 1px solid #eee; margin: 30px 0;">
    
    <p style="font-size: 12px; color: #999;">
        Ez egy automatikus üzenet, kérjük ne válaszoljon rá.
    </p>
</body>
</html>
