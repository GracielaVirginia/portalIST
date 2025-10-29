<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\AssistantQa;
use Illuminate\Http\Request;

class AssistantPublicController extends Controller
{
    public function list(Request $request)
    {
        $q = trim($request->query('q', ''));
        $items = AssistantQa::query()
            ->where('is_active', true)
            ->when($q !== '', function ($qb) use ($q) {
                $qb->where(function($w) use ($q) {
                    $w->where('question', 'like', "%{$q}%")
                      ->orWhere('answer', 'like', "%{$q}%")
                      ->orWhere('category', 'like', "%{$q}%");
                });
            })
            ->ordered()
            ->limit(200)
            ->get(['id','question','answer']);
        return response()->json(['ok' => true, 'items' => $items]);
    }
}
