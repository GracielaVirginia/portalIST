<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Horario;
use App\Models\Profesional;

class HorariosSeeder extends Seeder
{
    public function run(): void
    {
        $plantilla = [
            // Mañana y tarde en días laborales típicos
            ['dia' => 'lunes',     'inicio' => '09:00', 'fin' => '13:00', 'bloque' => 30, 'tipo' => 'Mañana'],
            ['dia' => 'lunes',     'inicio' => '14:00', 'fin' => '18:00', 'bloque' => 30, 'tipo' => 'Tarde'],
            ['dia' => 'martes',    'inicio' => '09:00', 'fin' => '13:00', 'bloque' => 30, 'tipo' => 'Mañana'],
            ['dia' => 'martes',    'inicio' => '14:00', 'fin' => '18:00', 'bloque' => 30, 'tipo' => 'Tarde'],
            ['dia' => 'miércoles', 'inicio' => '09:00', 'fin' => '13:00', 'bloque' => 30, 'tipo' => 'Mañana'],
            ['dia' => 'jueves',    'inicio' => '09:00', 'fin' => '13:00', 'bloque' => 30, 'tipo' => 'Mañana'],
            ['dia' => 'jueves',    'inicio' => '14:00', 'fin' => '18:00', 'bloque' => 30, 'tipo' => 'Tarde'],
            ['dia' => 'viernes',   'inicio' => '09:00', 'fin' => '13:00', 'bloque' => 30, 'tipo' => 'Mañana'],
        ];

        $profesionales = Profesional::with('sucursal')->get();
        foreach ($profesionales as $p) {
            foreach ($plantilla as $f) {
                // Evita duplicados si corres varias veces
                $exists = Horario::where('profesional_id', $p->id)
                    ->where('dia_semana', $f['dia'])
                    ->where('hora_inicio', $f['inicio'])
                    ->where('hora_fin', $f['fin'])
                    ->exists();

                if ($exists) continue;

                Horario::create([
                    'idempresa'       => 1,
                    'idsucursal'      => $p->idsucursal,
                    'profesional_id'  => $p->id,
                    'tipo'            => $f['tipo'],
                    'dia_semana'      => $f['dia'],
                    'hora_inicio'     => $f['inicio'],
                    'hora_fin'        => $f['fin'],
                    'duracion_bloque' => $f['bloque'],
                ]);
            }
        }

        $this->command->info('✅ Horarios base creados para los profesionales existentes.');
    }
}
