<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE `users` MODIFY `role` ENUM('admin','cashier','kitchen','waiter') NOT NULL DEFAULT 'cashier'");
        DB::statement("ALTER TABLE `transactions` MODIFY `status` ENUM('paid','cooking','ready','served','done','cancelled') NOT NULL DEFAULT 'paid'");
    }

    public function down(): void
    {
        DB::table('users')->where('role', 'waiter')->update(['role' => 'cashier']);
        DB::table('transactions')->where('status', 'ready')->update(['status' => 'cooking']);
        DB::table('transactions')->where('status', 'served')->update(['status' => 'done']);

        DB::statement("ALTER TABLE `users` MODIFY `role` ENUM('admin','cashier','kitchen') NOT NULL DEFAULT 'cashier'");
        DB::statement("ALTER TABLE `transactions` MODIFY `status` ENUM('paid','cooking','done','cancelled') NOT NULL DEFAULT 'paid'");
    }
};