<?php

namespace App\Http\Controllers;

use App\Models\PortalSection;

class PortalPageController extends Controller
{
    /**
     * Página pública: /conoce-mas
     * Carga bloques visibles/publicados y entrega la vista.
     */
    public function conoceMas()
    {
        $bloques = PortalSection::bloques('conoce-mas'); // colección agrupada por 'tipo'
        $heroBG = $hero['fondo_url'] ?? asset('images/default-hero.jpg');

        $hero         = optional($bloques->get('hero'))?->first()?->contenido ?? [];
        $beneficios   = $bloques->get('beneficios')?->first()?->contenido ?? [];
        $comoFunciona = $bloques->get('como_funciona')?->first()?->contenido ?? [];
        $novedades    = $bloques->get('novedades')?->first()?->contenido ?? [];
        $testimonios  = $bloques->get('testimonios')?->first()?->contenido ?? [];
        $kpis         = $bloques->get('kpis')?->first()?->contenido ?? [];
        $seguridad    = $bloques->get('seguridad')?->first()?->contenido ?? [];

        // Opcional: branding y opciones extra podrían venir de otras secciones o system_settings
        $branding  = ['logo_url' => asset('favicon.ico')];
        $opciones  = [
            'mostrar_beneficios'      => !empty($beneficios),
            'mostrar_como_funciona'   => !empty($comoFunciona),
            'mostrar_novedades'       => !empty($novedades),
            'mostrar_testimonios'     => !empty($testimonios),
            'mostrar_kpis'            => !empty($kpis),
            'mostrar_seguridad'       => !empty($seguridad),
        ];

        return view('portal.conoce-mas', compact(
            'hero','beneficios','comoFunciona','novedades','testimonios','kpis','seguridad','branding','opciones','heroBG'
        ));
    }
}
