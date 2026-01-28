<?php

namespace App\Models;

use App\Enums\AppointmentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Appointment extends Model
{
    protected $fillable = [
        'service_id',
        'voucher_id',
        'user_name',
        'user_email',
        'user_phone',
        'start_time',
        'end_time',
        'buffer_at_booking',
        'status',
        'price_at_booking',
        'deposit_at_booking',
        'voucher_discount',
        'notes',
        'locale',
        'reminder_sent_at',
    ];

    protected function casts(): array
    {
        return [
            'start_time' => 'datetime',
            'end_time' => 'datetime',
            'buffer_at_booking' => 'integer',
            'status' => AppointmentStatus::class,
            'price_at_booking' => 'decimal:2',
            'deposit_at_booking' => 'decimal:2',
            'voucher_discount' => 'decimal:2',
            'reminder_sent_at' => 'datetime',
        ];
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function voucher(): BelongsTo
    {
        return $this->belongsTo(Voucher::class, 'voucher_id');
    }

    public function tokens(): HasMany
    {
        return $this->hasMany(BookingToken::class, 'appointment_id');
    }

    public function transaction(): MorphOne
    {
        return $this->morphOne(Transaction::class, 'payable');
    }
}
