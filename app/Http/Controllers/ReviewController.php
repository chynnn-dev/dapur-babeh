<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\SiteContent;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    // Pelanggan mengirim ulasan
    public function store(Request $request)
    {
        // Jebakan bot: kolom tersembunyi harus kosong
        if ($request->filled('website')) {
            return back();
        }

        $data = $request->validate([
            'name'    => ['required', 'string', 'max:60'],
            'rating'  => ['required', 'integer', 'between:1,5'],
            'comment' => ['required', 'string', 'min:5', 'max:500'],
        ]);

        // Atur di admin: review_auto_approve = '1' agar ulasan langsung tampil
        $auto = (SiteContent::map()['review_auto_approve'] ?? '0') === '1';

        Review::create($data + ['is_approved' => $auto]);

        return back()->with('review_ok', $auto
            ? 'Terima kasih! Ulasan Anda sudah tampil.'
            : 'Terima kasih! Ulasan Anda akan tampil setelah disetujui admin.');
    }
}