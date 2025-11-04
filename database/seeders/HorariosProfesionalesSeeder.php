<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HorarioProfesional;
use App\Models\User;

class HorariosProfesionalesSeeder extends Seeder
{
    public function run(): void
    {
        // Busca un profesional existente o crea uno
        $doctor = User::where('idrol', 3)->first();
        if (!$doctor) {
            $doctor = User::factory()->create([
                'NOMBRE' => 'Ana',
                'APELLIDO' => 'Cortés',
                'EMAIL' => 'ana.cortes@clinicademo.cl',
                'TIPO' => 'Médico General',
                'idrol' => 3,
            ]);
        }

        $idEmpresa = $doctor->idempresa ?? 1;

        // Configuración de horario base
        $dias = [
            ['dia' => 'lunes',     'inicio' => '08:30', 'fin' => '13:00'],
            ['dia' => 'lunes',     'inicio' => '14:00', 'fin' => '18:00'],
            ['dia' => 'martes',    'inicio' => '08:30', 'fin' => '13:00'],
            ['dia' => 'martes',    'inicio' => '14:00', 'fin' => '18:00'],
            ['dia' => 'miércoles', 'inicio' => '09:00', 'fin' => '15:00'],
            ['dia' => 'jueves',    'inicio' => '08:30', 'fin' => '13:00'],
            ['dia' => 'jueves',    'inicio' => '14:00', 'fin' => '18:00'],
            ['dia' => 'viernes',   'inicio' => '08:30', 'fin' => '13:00'],
        ];

        foreach ($dias as $d) {
            HorarioProfesional::create([
                'idempresa'       => $idEmpresa,
                'idprofesional'   => $doctor->id,
                'tipo'            => 'Consulta médica',
                'dia_semana'      => $d['dia'],
                'hora_inicio'     => $d['inicio'],
                'hora_fin'        => $d['fin'],
                'duracion_bloque' => 30,
            ]);
        }

        $this->command->info('✅ Horarios base creados para '.$doctor->NOMBRE);
    }
}
