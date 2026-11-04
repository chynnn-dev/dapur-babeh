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
    Schema::create('posts', function (Blueprint $table) {
        $table->id();
        $table->string('type', 20)->index();
        $table->string('title')->nullable();
        $table->string('caption', 500)->nullable();
        $table->string('image');
        $table->unsignedInteger('sort_order')->default(0);
        $table->boolean('is_published')->default(true);
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('posts');
}
};
