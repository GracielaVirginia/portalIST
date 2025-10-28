<?php
// app/Http/Controllers/Admin/ReviewsAdminController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;

class ReviewsAdminController extends Controller
{

public function index()
{
    $reviews = \App\Models\Review::with(['user:id,name,email'])
        ->latest()->get();

    // Recuento por rating
    $raw = \App\Models\Review::selectRaw('rating, COUNT(*) as c')
        ->groupBy('rating')->pluck('c','rating');

    // Normalizamos 1..5
    $counts = [];
    for ($i = 1; $i <= 5; $i++) {
        $counts[$i] = (int)($raw[$i] ?? 0);
    }

    $total = array_sum($counts);
    $avg   = (float)\App\Models\Review::avg('rating');

    return view('admin.reviews.index', compact('reviews','counts','total','avg'));
}


    public function show(Review $review)
    {
        $review->load(['user:id,name,email']);
        return view('admin.reviews.show', compact('review'));
    }
}
