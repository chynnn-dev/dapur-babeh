<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Post;
use App\Models\SiteContent;

class HomeController extends Controller
{
    public function index()
    {
        $c = SiteContent::map();

        // Menu asli dari data kasir, cukup diinput sekali di Admin → Menu
        $menuItems = Menu::with('category')
            ->where('show_on_home', true)
            ->get()
            ->sortBy(fn ($m) => mb_strtolower($m->category->name . ' ' . $m->name))
            ->values();

        return view('home', [
            'c'              => $c,
            'waUrl'          => SiteContent::whatsappUrl($c),
            'slides'         => Post::published()->ofType('hero')->ordered()->get(),
            'menuItems'      => $menuItems,
            'menuCategories' => $menuItems->pluck('category')->unique('id')->sortBy('name')->values(),
            'galleries'      => Post::published()->ofType('gallery')->ordered()->get(),
        ]);
    }
}