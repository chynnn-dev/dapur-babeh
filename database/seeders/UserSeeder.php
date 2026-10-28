<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name'     => 'Owner Babeh',
            'username' => 'admin',
            'password' => 'password',
            'role'     => 'admin',
        ]);

        User::create([
            'name'     => 'Kasir 1',
            'username' => 'kasir1',
            'password' => 'password',
            'role'     => 'cashier',
        ]);

        User::create([
            'name'     => 'Koki 1',
            'username' => 'dapur1',
            'password' => 'password',
            'role'     => 'kitchen',
        ]);
    }
}