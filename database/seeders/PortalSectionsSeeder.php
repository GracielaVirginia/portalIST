<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PortalSectionsSeeder extends Seeder
{
    public function run(): void
    {
        $page = 'conoce-mas';

        $bloques = [
            [
                'tipo' => 'hero',
                'titulo' => 'Portal Salud IST,',
                'subtitulo' => 'toda tu información médica en un solo lugar.',
                'contenido' => json_encode([
                    'fondo_url' => 'images/bg-purple.png',
                    'overlay_opacity' => 0.35,
                    'cta_texto' => 'Conoce más del Portal',
                    'cta_url' => '#'
                ]),
                'posicion' => 1,
            ],
            [
                'tipo' => 'beneficios',
                'titulo' => 'Beneficios principales',
                'contenido' => json_encode([
                    ['icon' => '💬', 'titulo' => 'Citas y atención médica', 'texto' => '', 'url' => '#'],
                    ['icon' => '🩺', 'titulo' => 'Salud preventiva', 'texto' => '', 'url' => '#'],
                    ['icon' => '👨‍👩‍👧‍👦', 'titulo' => 'Gestión de tu familia', 'texto' => '', 'url' => '#'],
                ]),
                'posicion' => 2,
            ],
            [
                'tipo' => 'como_funciona',
                'titulo' => 'Cómo funciona',
                'contenido' => json_encode([
                    ['icon' => '①', 'titulo' => 'Ingresa al Portal', 'texto' => 'Usa tu RUT y clave para acceder.'],
                    ['icon' => '②', 'titulo' => 'Agenda tus atenciones', 'texto' => 'Reserva citas y gestiona tus exámenes.'],
                    ['icon' => '③', 'titulo' => 'Consulta tus resultados', 'texto' => 'Descarga informes y recetas fácilmente.'],
                ]),
                'posicion' => 3,
            ],
            [
                'tipo' => 'novedades',
                'titulo' => 'Novedades',
                'contenido' => json_encode([
                    ['titulo' => 'Nuevo acceso móvil', 'resumen' => 'Ahora puedes ingresar desde tu celular.', 'url' => '#', 'fecha' => now()->toDateString()],
                    ['titulo' => 'Campaña de prevención', 'resumen' => 'Conoce nuestras nuevas iniciativas de salud preventiva.', 'url' => '#', 'fecha' => now()->toDateString()],
                ]),
                'posicion' => 4,
            ],
            [
                'tipo' => 'testimonios',
                'titulo' => 'Testimonios',
                'contenido' => json_encode([
                    ['texto' => 'El portal me ahorra mucho tiempo en mis controles médicos.', 'autor' => 'Paciente IST', 'ubicacion' => 'Valparaíso', 'avatar' => 'images/placeholder.png'],
                    ['texto' => 'Puedo revisar los informes de mi familia fácilmente.', 'autor' => 'Ana R.', 'ubicacion' => 'Santiago', 'avatar' => 'images/placeholder.png'],
                ]),
                'posicion' => 5,
            ],
            [
                'tipo' => 'kpis',
                'titulo' => 'Datos destacados',
                'contenido' => json_encode([
                    ['valor' => '120K+', 'etiqueta' => 'Pacientes activos'],
                    ['valor' => '98%', 'etiqueta' => 'Satisfacción'],
                    ['valor' => '24/7', 'etiqueta' => 'Acceso en línea'],
                ]),
                'posicion' => 6,
            ],
            [
                'tipo' => 'seguridad',
                'titulo' => 'Seguridad y privacidad',
                'contenido' => json_encode([
                    'texto' => 'Cuidamos tus datos con cifrado y controles de acceso.',
                    'links' => [
                        ['label' => 'Política de privacidad', 'url' => '#'],
                        ['label' => 'Términos del servicio', 'url' => '#'],
                    ],
                ]),
                'posicion' => 7,
            ],
        ];

        foreach ($bloques as $b) {
            DB::table('portal_sections')->insert([
                'page_slug' => $page,
                'tipo' => $b['tipo'],
                'titulo' => $b['titulo'] ?? null,
                'subtitulo' => $b['subtitulo'] ?? null,
                'contenido' => $b['contenido'] ?? null,
                'posicion' => $b['posicion'],
                'visible' => true,
                'publicar_desde' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
