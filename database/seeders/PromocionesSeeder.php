<?php
// database/seeders/PromocionesSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PromocionesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('promociones')->insert([
            [
                'titulo' => 'Chequeo Preventivo 2025',
                'subtitulo' => 'Descuento especial en exámenes preventivos durante noviembre.',
                'contenido_html' => '<p>Realiza tu chequeo preventivo y obtén un <strong>20% de descuento</strong> en laboratorio.</p>',
                'imagen_path' => 'images/promos/chequeo-preventivo.jpg',
                'cta_texto' => 'Agenda tu examen',
                'cta_url' => '/portal/agenda',
                'activo' => true,
                'destacada' => true,   // <-- esta irá al banner por defecto
                'orden' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'titulo' => 'Radiología Digital',
                'subtitulo' => 'Resultados más rápidos con 15% de descuento.',
                'contenido_html' => '<p>Aprovecha nuestra <strong>radiología digital</strong> con informes en línea.</p>',
                'imagen_path' => 'images/promos/radiologia-digital.jpg',
                'cta_texto' => 'Ver detalles',
                'cta_url' => '/portal/promociones/radiologia',
                'activo' => true,
                'destacada' => false,
                'orden' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
