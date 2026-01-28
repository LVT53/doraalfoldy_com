<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
    ];

    private static array $encryptedKeys = [
        'barion_pos_key',
    ];

    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = self::where('key', $key)->first();

        if (! $setting) {
            return $default;
        }

        if (in_array($key, self::$encryptedKeys)) {
            return Crypt::decryptString($setting->value);
        }

        return $setting->value;
    }

    public static function set(string $key, mixed $value): void
    {
        if (in_array($key, self::$encryptedKeys)) {
            $value = Crypt::encryptString($value);
        }

        self::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }
}
