<?php
// app/Http/Controllers/PortalPromocionesController.php

namespace App\Http\Controllers;

use App\Models\Promocion;

class PortalPromocionesController extends Controller
{
    // Muestra la promocion destacada (para el banner) y lista de activas
    public function index()
    {
        $destacada = Promocion::destacada()->first();
        $promos = Promocion::activas()->get();

        // Vista sugerida: resources/views/portal/promociones.blade.php
        return view('portal.promociones', compact('destacada', 'promos'));
    }

    public function show(Promocion $promocion)
    {
        abort_unless($promocion->activo, 404);
        // Vista sugerida: resources/views/portal/promocion-show.blade.php
        return view('portal.promocion-show', compact('promocion'));
    }
}
