<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RestaurantTable;
use App\Models\Setting;

class SettingController extends Controller
{
    public function index()
    {
        return view('admin.settings.index', [
            'tableManagement' => Setting::isActive(Setting::TABLE_MANAGEMENT),
            'tableCount'      => RestaurantTable::count(),
            'occupiedCount'   => RestaurantTable::where('status', 'occupied')->count(),
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
}