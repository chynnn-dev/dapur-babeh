<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteContent;
use Illuminate\Http\Request;

class FrontPageController extends Controller
{
    public function edit()
    {
        return view('admin.front.edit', ['c' => SiteContent::map()]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'site_name'        => ['required', 'string', 'max:60'],
            'hero_title'       => ['required', 'string', 'max:100'],
            'hero_subtitle'    => ['nullable', 'string', 'max:160'],
            'about_title'      => ['required', 'string', 'max:80'],
            'about_text'       => ['nullable', 'string', 'max:3000'],
            'open_hours'       => ['nullable', 'string', 'max:100'],
            'address'          => ['nullable', 'string', 'max:300'],
            'maps_url'         => ['nullable', 'url', 'max:500'],
            'whatsapp_number'  => ['nullable', 'regex:/^[0-9+\s\-()]{8,20}$/'],
            'whatsapp_message' => ['nullable', 'string', 'max:300'],
        ], [
            'whatsapp_number.regex' => 'Nomor WhatsApp hanya boleh berisi angka, spasi, tanda + atau - (8 sampai 20 karakter).',
            'maps_url.url'          => 'Link Google Maps harus diawali http:// atau https://.',
        ]);

        SiteContent::put($data);

        return back()->with('success', 'Halaman depan berhasil diperbarui.');
    }
}