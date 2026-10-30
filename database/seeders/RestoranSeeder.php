<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Menu;
use App\Models\RestaurantTable;
use Illuminate\Database\Seeder;

class RestoranSeeder extends Seeder
{
    public function run(): void
    {
        $makanan = Category::create(['name' => 'Makanan']);
        $minuman = Category::create(['name' => 'Minuman']);

        Menu::create(['category_id' => $makanan->id, 'name' => 'Nasi Goreng', 'price' => 20000]);
        Menu::create(['category_id' => $makanan->id, 'name' => 'Mie Ayam',    'price' => 15000]);
        Menu::create(['category_id' => $minuman->id, 'name' => 'Es Teh',      'price' => 5000]);
        Menu::create(['category_id' => $minuman->id, 'name' => 'Kopi Hitam',  'price' => 8000]);

        foreach (['1', '2', '3', '4', '5'] as $number) {
            RestaurantTable::create(['table_number' => $number]);
        }
    }
}