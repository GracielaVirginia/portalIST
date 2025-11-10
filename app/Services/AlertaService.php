<?php

namespace App\Services;

use App\Models\Alerta;
use Illuminate\Http\Request;

class AlertaService
{
    /**
     * Registra una alerta en la base de datos.
     */
    public function registrar(string $tipo, array $data = [], ?Request $request = null): Alerta
    {
        $req = $request ?? request();

        return Alerta::create([
            'user_id'    => $data['user_id']    ?? null,
            'tipo'       => $tipo,
            'intentos'   => $data['intentos']   ?? 0,
            'ip'         => $data['ip']         ?? $req->ip(),
            'user_agent' => $data['user_agent'] ?? $req->userAgent(),
            'documento'  => $data['documento']  ?? null,
            'extra'      => $data['extra']      ?? null,
            'ocurrio_en' => now(),
        ]);
    }
}
