<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = ['name', 'rating', 'comment', 'is_approved'];

    protected $casts = ['is_approved' => 'boolean'];

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('is_approved', true);
    }
}