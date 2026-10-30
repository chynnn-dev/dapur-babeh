<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionItem extends Model
{
    protected $fillable = ['transaction_id', 'menu_id', 'quantity', 'subtotal', 'notes'];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    // Harga satuan diturunkan dari subtotal / jumlah
    public function getPriceAttribute(): int
    {
        return $this->quantity > 0 ? intdiv($this->subtotal, $this->quantity) : 0;
    }
}