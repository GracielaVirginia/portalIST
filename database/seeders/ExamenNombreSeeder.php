<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ExamenNombre;
use App\Models\Especialidad;

class ExamenNombreSeeder extends Seeder
{
    public function run()
    {
        $especialidades = [
            'Radiología' => [
                ['codigo' => 'RX-TX',    'nombre' => 'Radiografía de Tórax',      'tipo' => 'IMAGEN'],
                ['codigo' => 'ECO-ABD',  'nombre' => 'Ecografía Abdominal',       'tipo' => 'IMAGEN'],
                ['codigo' => 'TAC-CR',   'nombre' => 'TAC de Cráneo',             'tipo' => 'IMAGEN'],
            ],
            'Laboratorio' => [
                ['codigo' => 'GLU',      'nombre' => 'Glucosa en sangre',         'tipo' => 'LAB'],
                ['codigo' => 'HB',       'nombre' => 'Hemograma completo',        'tipo' => 'LAB'],
                ['codigo' => 'TSH',      'nombre' => 'TSH',                       'tipo' => 'LAB'],
                ['codigo' => 'HBA1C',    'nombre' => 'Hemoglobina Glicosilada',   'tipo' => 'LAB'],
            ],
            'Cardiología' => [
                ['codigo' => 'ECG',      'nombre' => 'Electrocardiograma',        'tipo' => 'PROCEDIMIENTO'],
                ['codigo' => 'ECO-CARD', 'nombre' => 'Ecocardiograma',            'tipo' => 'IMAGEN'],
                ['codigo' => 'HOLTER',   'nombre' => 'Holter 24h',                'tipo' => 'PROCEDIMIENTO'],
            ],
            'Medicina General' => [
                ['codigo' => 'CTRL-GEN', 'nombre' => 'Control General',           'tipo' => 'CONSULTA'],
            ],
            'Endocrinología' => [
                ['codigo' => 'HBA1C',    'nombre' => 'Hemoglobina Glicosilada',   'tipo' => 'LAB'],
                ['codigo' => 'GLU',      'nombre' => 'Glucosa en sangre',         'tipo' => 'LAB'],
            ],
        ];

        foreach ($especialidades as $especialidadNombre => $examenes) {
            $especialidad = Especialidad::where('name', $especialidadNombre)->first();
            if ($especialidad) {
                foreach ($examenes as $examen) {
                    ExamenNombre::create([
                        'codigo' => $examen['codigo'],
                        'nombre' => $examen['nombre'],
                        'tipo' => $examen['tipo'],
                        'especialidad_id' => $especialidad->id,
                    ]);
                }
            }
        }
    }
}
