<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $fillable = ['category_id', 'name', 'price', 'image', 'is_available', 'show_on_home'];

    protected $casts = [
        'is_available' => 'boolean',
        'show_on_home' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }

    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }
}