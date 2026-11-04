<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('payment_reference', 100)->nullable()->after('payment_method');
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->string('value')->nullable()->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('payment_reference');
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('value');
        });
    }
};