<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ControlesController extends Controller
{
    public function store(Request $request)
    {
        // aquí luego procesas el control de tensión, glucosa, etc.
        return response()->json(['ok' => true]);
    }
}
