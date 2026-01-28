<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ScheduleException extends Model
{
    protected $fillable = [
        'date',
        'reason',
        'is_closed',
        'custom_start_time',
        'custom_end_time',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'is_closed' => 'boolean',
        ];
    }

    public function scopeForDate(Builder $query, string $date): Builder
    {
        return $query->where('date', $date);
    }
}
