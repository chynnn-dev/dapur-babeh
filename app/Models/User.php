<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    public const ROLES = [
    'admin'   => 'Admin / Owner',
    'cashier' => 'Kasir',
    'kitchen' => 'Dapur',
];
    protected $fillable = ['name', 'username', 'password', 'role'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return ['password' => 'hashed'];
    }

    public function hasRole(string ...$roles): bool
    {
        return in_array($this->role, $roles, true);
    }

    public function homeRoute(): string
    {
        return match ($this->role) {
            'admin'   => route('admin.dashboard'),
            'cashier' => route('cashier.pos'),
            'kitchen' => route('kitchen.index'),
        };
    }

    public function transactions()
{
    return $this->hasMany(Transaction::class, 'cashier_id');
}
}