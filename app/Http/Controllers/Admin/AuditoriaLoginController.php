<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuditoriaLoginController extends Controller
{
    public function index(Request $request)
    {
        // Aquí luego pondrás la lógica de filtros, conteos, etc.
        $dateFrom = $request->input('from', now()->format('Y-m-d'));
        $dateTo   = $request->input('to', now()->format('Y-m-d'));

        return view('admin.auditoria-logins', compact('dateFrom', 'dateTo'));
    }
}
