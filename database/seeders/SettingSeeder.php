<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        Setting::create([
            'key_name'  => Setting::TABLE_MANAGEMENT,
            'is_active' => false,
        ]);

        Setting::create(['key_name' => Setting::PAYMENT_QRIS,  'is_active' => false]);
Setting::create(['key_name' => Setting::PAYMENT_DEBIT, 'is_active' => false]);
    }
}