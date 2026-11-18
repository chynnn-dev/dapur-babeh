<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('midtrans_payments', function (Blueprint $table) {
            $table->id();
            $table->string('order_id', 50)->unique();          // order_id yang dikirim ke Midtrans
            $table->unsignedBigInteger('cashier_id')->index();
            $table->unsignedBigInteger('table_id')->nullable();
            $table->unsignedBigInteger('total_amount');
            $table->json('items');                              // isi keranjang saat pembayaran dibuat
            $table->string('snap_token')->nullable();
            $table->string('status', 20)->default('pending');   // pending, paid, failed, expired, cancelled
            $table->string('payment_type', 50)->nullable();     // qris, gopay, credit_card, dst.
            $table->unsignedBigInteger('transaction_id')->nullable()->index(); // terisi setelah lunas
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('midtrans_payments');
    }
};