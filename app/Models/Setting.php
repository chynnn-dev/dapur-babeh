<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    public const TABLE_MANAGEMENT = 'table_management';
    public const PAYMENT_QRIS     = 'payment_qris';
    public const PAYMENT_DEBIT    = 'payment_debit';
    public const QRIS_IMAGE       = 'qris_image';

    protected $fillable = ['key_name', 'is_active', 'value'];

    protected $casts = ['is_active' => 'boolean'];

    public static function isActive(string $key): bool
    {
        return (bool) static::where('key_name', $key)->value('is_active');
    }

    public static function setActive(string $key, bool $value): void
    {
        static::updateOrCreate(['key_name' => $key], ['is_active' => $value]);
    }

    public static function getValue(string $key): ?string
    {
        return static::where('key_name', $key)->value('value');
    }

    public static function setValue(string $key, ?string $value): void
    {
        static::updateOrCreate(['key_name' => $key], ['value' => $value]);
    }

    public static function qrisUrl(): ?string
    {
        $path = static::getValue(self::QRIS_IMAGE);

        return $path ? asset('storage/' . $path) : null;
    }
}