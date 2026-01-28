<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeProfile extends Model
{
    protected $fillable = [
        'name',
        'bio',
        'image_path',
        'instagram_url',
    ];

    public static function profile(): ?self
    {
        return self::first();
    }
}
