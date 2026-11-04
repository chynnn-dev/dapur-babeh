<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('site_contents', function (Blueprint $table) {
        $table->id();
        $table->string('key_name')->unique();
        $table->text('value')->nullable();
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('site_contents');
}
};
