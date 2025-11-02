<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SystemSettingsSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('system_settings')->upsert([
            [
                'clave' => 'home_section_tipo',
                'valor' => 'banner', // puede ser "cards"
                'descripcion' => 'Tipo de bloque inferior en pantalla de inicio (banner o cards)',
                'tipo' => 'string',
            ],
            [
                'clave' => 'home_banner_titulo',
                'valor' => '¡Conoce las nuevas funcionalidades del Portal Salud IST!',
                'descripcion' => 'Título del banner principal',
                'tipo' => 'string',
            ],
            [
                'clave' => 'home_banner_texto',
                'valor' => 'Accede desde tu celular y gestiona tus atenciones médicas fácilmente.',
                'descripcion' => 'Texto del banner principal',
                'tipo' => 'string',
            ],
            [
                'clave' => 'home_banner_cta',
                'valor' => 'Descúbrelo aquí →',
                'descripcion' => 'Texto del botón CTA del banner',
                'tipo' => 'string',
            ],
            [
                'clave' => 'home_banner_url',
                'valor' => '/promociones',
                'descripcion' => 'Enlace del botón del banner',
                'tipo' => 'string',
            ],
            [
                'clave' => 'home_cards',
                'valor' => json_encode([
                    ['icon' => '💬', 'titulo' => 'Atención personalizada', 'texto' => 'Agenda tus consultas de forma rápida y segura.'],
                    ['icon' => '🩺', 'titulo' => 'Salud preventiva', 'texto' => 'Programas y controles para tu bienestar.'],
                    ['icon' => '📱', 'titulo' => 'Resultados en línea', 'texto' => 'Consulta informes y exámenes cuando quieras.'],
                ]),
                'descripcion' => 'JSON con las tres cards informativas',
                'tipo' => 'json',
            ],
        ], ['clave']);
    }
}
