<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RestaurantTable extends Model
{
    protected $table = 'tables';

    protected $fillable = ['table_number', 'status'];

    public function transactions()
{
    return $this->hasMany(Transaction::class, 'table_id');
}
}
