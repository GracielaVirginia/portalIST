<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Noticia;

class NoticiasTableSeeder extends Seeder
{
    public function run(): void
    {
        Noticia::create([
            'titulo'    => 'Chequeo general: cuida tu salud preventiva',
            'bajada'    => 'Recuerda registrar tus controles de tensión y glucosa. Si tienes lecturas altas por 3 días seguidos, agenda un control.',
            'contenido' => 'Contenido completo de la noticia...',
            'imagen'    => 'https://images.unsplash.com/photo-1585421514738-01798e348b17?q=80&w=1200&auto=format&fit=crop',
            'destacada' => true,
        ]);

        Noticia::create([
            'titulo'    => 'Ejercicio diario: 30 minutos que cambian tu salud',
            'bajada'    => 'Incorporar actividad física regular reduce el riesgo de enfermedades crónicas y mejora el bienestar mental.',
            'contenido' => 'Más detalles sobre la importancia del ejercicio...',
            'imagen'    => 'https://images.unsplash.com/photo-1599058917212-d750089bc07e?q=80&w=1200&auto=format&fit=crop',
            'destacada' => false,
        ]);
    }
}
