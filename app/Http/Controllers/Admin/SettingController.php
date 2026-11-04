<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RestaurantTable;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        return view('admin.settings.index', [
            'tableManagement' => Setting::isActive(Setting::TABLE_MANAGEMENT),
            'tableCount'      => RestaurantTable::count(),
            'occupiedCount'   => RestaurantTable::where('status', 'occupied')->count(),
            'qrisOn'          => Setting::isActive(Setting::PAYMENT_QRIS),
            'debitOn'         => Setting::isActive(Setting::PAYMENT_DEBIT),
            'qrisImage'       => Setting::qrisUrl(),
        ]);
    }

    public function toggleTableManagement()
    {
        $new = ! Setting::isActive(Setting::TABLE_MANAGEMENT);

        if ($new && RestaurantTable::count() === 0) {
            return back()->with('error', 'Tambahkan minimal satu meja dulu sebelum mengaktifkan fitur ini.');
        }

        Setting::setActive(Setting::TABLE_MANAGEMENT, $new);

        return back()->with('success', 'Manajemen Meja sekarang ' . ($new ? 'AKTIF (ON)' : 'NONAKTIF (OFF)') . '.');
    }

    public function togglePayment(string $method)
    {
        $keys = ['qris' => Setting::PAYMENT_QRIS, 'debit' => Setting::PAYMENT_DEBIT];
        abort_unless(isset($keys[$method]), 404);

        $new = ! Setting::isActive($keys[$method]);

        if ($new && $method === 'qris' && ! Setting::getValue(Setting::QRIS_IMAGE)) {
            return back()->with('error', 'Unggah gambar QRIS toko dulu sebelum mengaktifkan QRIS.');
        }

        Setting::setActive($keys[$method], $new);

        return back()->with('success', strtoupper($method) . ' sekarang ' . ($new ? 'AKTIF' : 'NONAKTIF') . '.');
    }

    public function uploadQris(Request $request)
    {
        $request->validate([
            'qris_image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'qris_image.required' => 'Pilih file gambar QRIS terlebih dahulu.',
            'qris_image.image'    => 'File harus berupa gambar.',
            'qris_image.max'      => 'Ukuran gambar maksimal 2 MB.',
        ]);

        $old = Setting::getValue(Setting::QRIS_IMAGE);
        if ($old) {
            Storage::disk('public')->delete($old);
        }

        Setting::setValue(Setting::QRIS_IMAGE, $request->file('qris_image')->store('qris', 'public'));

        return back()->with('success', 'Gambar QRIS berhasil diperbarui.');
    }

    public function removeQris()
    {
        $old = Setting::getValue(Setting::QRIS_IMAGE);
        if ($old) {
            Storage::disk('public')->delete($old);
        }

        Setting::setValue(Setting::QRIS_IMAGE, null);
        Setting::setActive(Setting::PAYMENT_QRIS, false); // tanpa gambar, QRIS ikut dimatikan

        return back()->with('success', 'Gambar QRIS dihapus dan QRIS dinonaktifkan.');
    }
}