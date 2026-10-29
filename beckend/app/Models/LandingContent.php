<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'description',
    ];

    /**
     * Ambil nilai content berdasarkan key dengan fallback default.
     */
    public static function getVal(string $key, ?string $default = null): ?string
    {
        $item = static::where('key', $key)->first();
        return $item ? $item->value : $default;
    }

    /**
     * Simpan atau update nilai content.
     */
    public static function setVal(string $key, ?string $value, string $type = 'text', string $group = 'landing', ?string $description = null): static
    {
        return static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'type' => $type,
                'group' => $group,
                'description' => $description,
            ]
        );
    }
}
