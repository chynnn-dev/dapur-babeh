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
    Schema::create('transactions', function (Blueprint $table) {
        $table->id();
        $table->string('invoice_number')->unique();
        $table->foreignId('table_id')->nullable()->constrained('tables')->nullOnDelete();
        $table->foreignId('cashier_id')->constrained('users')->restrictOnDelete();
        $table->unsignedInteger('total_amount');
        $table->unsignedInteger('cash_received');
        $table->unsignedInteger('change_amount');
        $table->string('payment_method')->default('cash');
        $table->enum('status', ['paid', 'cooking', 'done', 'cancelled'])->default('paid');
        $table->timestamps();

        $table->index(['status', 'created_at']);
    });
}

public function down(): void
{
    Schema::dropIfExists('transactions');
}
};
