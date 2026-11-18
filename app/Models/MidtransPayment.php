<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MidtransPayment extends Model
{
    protected $fillable = [
        'order_id', 'cashier_id', 'table_id', 'total_amount', 'items',
        'snap_token', 'status', 'payment_type', 'transaction_id',
    ];

    protected $casts = [
        'items' => 'array',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function cashier()
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function table()
    {
        return $this->belongsTo(RestaurantTable::class, 'table_id');
    }
}