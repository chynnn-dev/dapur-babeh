<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    public const TABLE_MANAGEMENT = 'table_management';

    protected $fillable = ['key_name', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public static function isActive(string $key): bool
    {
        return Cache::remember("setting.$key", 300, fn () =>
            (bool) static::where('key_name', $key)->value('is_active')
        );
    }

    public static function setActive(string $key, bool $value): void
    {
        static::updateOrCreate(['key_name' => $key], ['is_active' => $value]);
    }

    protected static function booted(): void
    {
        static::saved(fn ($setting) => Cache::forget("setting.{$setting->key_name}"));
    }
}