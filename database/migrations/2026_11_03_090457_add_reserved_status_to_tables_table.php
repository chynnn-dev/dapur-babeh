<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tables', function (Blueprint $table) {
            $table->enum('status', ['available', 'occupied', 'reserved'])->default('available')->change();
        });
    }

    public function down(): void
    {
        DB::table('tables')->where('status', 'reserved')->update(['status' => 'available']);

        Schema::table('tables', function (Blueprint $table) {
            $table->enum('status', ['available', 'occupied'])->default('available')->change();
        });
    }
};