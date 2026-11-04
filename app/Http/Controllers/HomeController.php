<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\SiteContent;

class HomeController extends Controller
{
    public function index()
    {
        $c = SiteContent::map();

        return view('home', [
            'c'         => $c,
            'waUrl'     => SiteContent::whatsappUrl($c),
            'slides'    => Post::published()->ofType('hero')->ordered()->get(),
            'menus'     => Post::published()->ofType('menu')->ordered()->get(),
            'galleries' => Post::published()->ofType('gallery')->ordered()->get(),
        ]);
    }
}