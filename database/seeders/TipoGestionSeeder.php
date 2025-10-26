<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TipoGestion;

class TipoGestionSeeder extends Seeder
{
    public function run()
    {
        $tipos = [
            ['name' => 'Consulta médica'],
            ['name' => 'Examen médico'],
            ['name' => 'Control'],
            ['name' => 'Urgencia/Emergencia'],
            ['name' => 'Licencia médica'],
            ['name' => 'Evaluación laboral'],
        ];

        foreach ($tipos as $tipo) {
            TipoGestion::create($tipo);
        }
    }
}
