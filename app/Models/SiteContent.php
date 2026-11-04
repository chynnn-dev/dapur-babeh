<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteContent extends Model
{
    protected $table = 'site_contents';

    protected $fillable = ['key_name', 'value'];

    // Teks bawaan jika Admin belum mengisi
    public const DEFAULTS = [
        'site_name'        => 'Dapur Babeh',
        'hero_title'       => 'Selamat Datang di Dapur Babeh',
        'hero_subtitle'    => 'Masakan hangat, dimasak sepenuh hati',
        'about_title'      => 'Tentang Kami',
        'about_text'       => 'Dapur Babeh adalah tempat keluarga berkumpul menikmati masakan yang hangat dan penuh rasa. Ceritakan kisah restoran Anda di sini melalui menu Halaman Depan di panel Admin.',
        'open_hours'       => 'Buka setiap hari 10.00 – 22.00 WIB',
        'address'          => '',
        'maps_url'         => '',
        'whatsapp_number'  => '',
        'whatsapp_message' => 'Halo Dapur Babeh, saya ingin reservasi meja.',
    ];

    // Gabungan isi buatan Admin dan teks bawaan
    public static function map(): array
    {
        $stored = static::pluck('value', 'key_name')->all();

        return array_merge(
            self::DEFAULTS,
            array_filter($stored, fn ($v) => $v !== null && $v !== '')
        );
    }

    public static function put(array $data): void
    {
        foreach ($data as $key => $value) {
            static::updateOrCreate(['key_name' => $key], ['value' => $value]);
        }
    }

    // Link WhatsApp untuk reservasi (null jika nomor belum diisi)
    public static function whatsappUrl(array $c): ?string
    {
        $digits = preg_replace('/\D/', '', $c['whatsapp_number'] ?? '');

        if ($digits === '') {
            return null;
        }

        if (str_starts_with($digits, '0')) {
            $digits = '62' . substr($digits, 1);
        }

        return 'https://wa.me/' . $digits . '?text=' . rawurlencode($c['whatsapp_message'] ?? '');
    }
}