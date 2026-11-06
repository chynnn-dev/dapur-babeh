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
        'maps_url'         => 'https://maps.app.goo.gl/zFAENhKFFdpmsuc46',
        'maps_embed'       => '',   // tempel kode/URL "Sematkan peta" dari Google Maps
        'review_auto_approve' => '0', // '1' = ulasan langsung tampil tanpa persetujuan admin
        'latitude'         => '',   // contoh: -7.7956
        'longitude'        => '',   // contoh: 110.3695
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

    // Nomor WhatsApp format internasional (null jika belum diisi)
    public static function whatsappNumber(array $c): ?string
    {
        $digits = preg_replace('/\D/', '', $c['whatsapp_number'] ?? '');

        if ($digits === '') {
            return null;
        }

        if (str_starts_with($digits, '0')) {
            $digits = '62' . substr($digits, 1);
        }

        return $digits;
    }

    // Link WhatsApp umum (null jika nomor belum diisi)
    public static function whatsappUrl(array $c): ?string
    {
        $number = static::whatsappNumber($c);

        return $number
            ? 'https://wa.me/' . $number . '?text=' . rawurlencode($c['whatsapp_message'] ?? '')
            : null;
    }

    // URL peta untuk iframe (koordinat diutamakan, lalu alamat)
    public static function mapEmbedUrl(array $c): string
    {
        // 1) Kode/URL sematan resmi dari Google Maps (paling akurat)
        $embed = trim($c['maps_embed'] ?? '');
        if ($embed !== '') {
            if (preg_match('/src=["\']([^"\']+)["\']/i', $embed, $m)) {
                $embed = html_entity_decode($m[1]);
            }
            if (preg_match('#^https://(www\.google\.com|maps\.google\.com)/maps#', $embed)) {
                return $embed;
            }
        }

        $lat = trim($c['latitude'] ?? '');
        $lng = trim($c['longitude'] ?? '');

        if (is_numeric($lat) && is_numeric($lng)) {
            $q = $lat . ',' . $lng;
        } elseif (!empty($c['address'])) {
            $q = $c['address'];
        } else {
            $q = $c['site_name'] ?? 'Restoran';   // cadangan; isi koordinat agar akurat
        }

        return 'https://maps.google.com/maps?q=' . rawurlencode($q) . '&z=16&output=embed';
    }
}