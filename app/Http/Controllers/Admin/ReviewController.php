<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;

class ReviewController extends Controller
{
    // Daftar semua ulasan
    public function index()
    {
        return view('admin.reviews.index', [
            'reviews' => Review::latest()->paginate(15),
            'pending' => Review::where('is_approved', false)->count(),
        ]);
    }

    // Setujui / sembunyikan ulasan
    public function toggle(Review $review)
    {
        $review->update(['is_approved' => ! $review->is_approved]);

        return back()->with('ok', $review->is_approved ? 'Ulasan ditampilkan.' : 'Ulasan disembunyikan.');
    }

    // Hapus ulasan
    public function destroy(Review $review)
    {
        $review->delete();

        return back()->with('ok', 'Ulasan dihapus.');
    }
}