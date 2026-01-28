<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('emails.reschedule_confirmation_subject', ['date' => $appointment->start_time->format('Y.m.d.'), 'time' => $appointment->start_time->format('H:i')]) }}</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <h1 style="color: #d4af37;">{{ __('emails.booking_confirmation_greeting', ['name' => $appointment->user_name]) }}</h1>
    
    <p>{{ __('emails.reschedule_confirmation_body') }}</p>
    
    <div style="background: #fff3cd; padding: 15px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #d4af37;">
        <h3 style="margin-top: 0; color: #856404;">Régi időpont</h3>
        <p><strong>Dátum:</strong> {{ $oldStartTime->format('Y.m.d.') }}</p>
        <p><strong>Időpont:</strong> {{ $oldStartTime->format('H:i') }} - {{ $oldEndTime->format('H:i') }}</p>
    </div>
    
    <div style="background: #d4edda; padding: 15px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #28a745;">
        <h3 style="margin-top: 0; color: #155724;">Új időpont</h3>
        <p><strong>{{ __('booking.service_details') }}:</strong> {{ $appointment->service?->name ?? 'Szolgáltatás' }}</p>
        <p><strong>Dátum:</strong> {{ $appointment->start_time->format('Y.m.d.') }}</p>
        <p><strong>Időpont:</strong> {{ $appointment->start_time->format('H:i') }} - {{ $appointment->end_time->format('H:i') }}</p>
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
                Újraütemezés
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
