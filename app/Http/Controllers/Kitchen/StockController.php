<?php

namespace App\Http\Controllers\Kitchen;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Menu;

class StockController extends Controller
{
    public function index()
    {
        $categories = Category::with(['menus' => fn ($q) => $q->orderBy('name')])
            ->orderBy('name')
            ->get();

        return view('kitchen.stock', compact('categories'));
    }

    public function toggle(Menu $menu)
    {
        $menu->update(['is_available' => ! $menu->is_available]);

        return back()->with('success', "{$menu->name} ditandai " . ($menu->is_available ? 'TERSEDIA' : 'HABIS') . '.');
    }
}