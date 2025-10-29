<?php

use App\Models\PortalValidacionConfig;

if (!function_exists('rutaValidacionActiva')) {
    /**
     * Devuelve la ruta correspondiente al modo de validación activo.
     */
    function rutaValidacionActiva(): string
    {
        $modo = PortalValidacionConfig::where('activo', true)->first();

        return match ($modo?->slug) {
            'sin-validacion' => route('validacion.sin'),
            'numero-caso'    => route('validacion.caso'),
            'tres-opciones'  => route('validacion.tres'),
            'creando-cuenta' => route('validacion.cuenta'),
            default           => route('validacion.sin'),
        };
    }
}
