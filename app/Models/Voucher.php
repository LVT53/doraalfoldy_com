<?php

namespace App\Models;

use App\Enums\VoucherType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'type',
        'value',
        'balance',
        'recipient_email',
        'used_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'type' => VoucherType::class,
            'value' => 'decimal:2',
            'balance' => 'decimal:2',
            'used_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function scopeValid(Builder $query, string $code): Builder
    {
        return $query->where('code', $code)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->where(function ($q) {
                $q->where(function ($sub) {
                    $sub->whereIn('type', ['percentage', 'fixed'])
                        ->whereNull('used_at');
                })
                    ->orWhere(function ($sub) {
                        $sub->where('type', 'gift_card')
                            ->where('balance', '>', 0);
                    });
            });
    }

    public function hasBalance(): bool
    {
        return $this->type === VoucherType::GIFT_CARD && $this->balance > 0;
    }

    public function isUsed(): bool
    {
        return in_array($this->type->value, ['percentage', 'fixed']) && $this->used_at !== null;
    }
}
