<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Especialidad;

class EspecialidadSeeder extends Seeder
{
    public function run()
    {
        $especialidades = [
            ['name' => 'Radiología'],
            ['name' => 'Laboratorio'],
            ['name' => 'Cardiología'],
            ['name' => 'Endocrinología'],
            ['name' => 'Medicina Interna'],
            ['name' => 'Psicología'],
        ];

        foreach ($especialidades as $especialidad) {
            Especialidad::create($especialidad);
        }
    }
}
