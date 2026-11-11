<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->string('image')->nullable()->change();
        });
    }

    public function down(): void
    {
        DB::table('posts')->whereNull('image')->update(['image' => '']);

        Schema::table('posts', function (Blueprint $table) {
            $table->string('image')->nullable(false)->change();
        });
    }
};