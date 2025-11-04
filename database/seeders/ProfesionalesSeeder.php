<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Profesional;
use App\Models\TipoProfesional;

class ProfesionalesSeeder extends Seeder
{
    public function run(): void
    {
        $idempresa = 1;

        $pairs = [
            'Nutricionista' => [
                ['nombres' => 'Camila',  'apellidos' => 'Fuentes',  'email' => 'camila.fuentes@demo.cl',  'telefono' => '+56 9 1111 1111'],
                ['nombres' => 'Diego',   'apellidos' => 'Morales',  'email' => 'diego.morales@demo.cl',   'telefono' => '+56 9 1111 1112'],
            ],
            'Ginecologo' => [
                ['nombres' => 'María',   'apellidos' => 'Rojas',    'email' => 'maria.rojas@demo.cl',     'telefono' => '+56 9 2222 2221'],
                ['nombres' => 'Felipe',  'apellidos' => 'Herrera',  'email' => 'felipe.herrera@demo.cl',  'telefono' => '+56 9 2222 2222'],
            ],
            'Psicologo' => [
                ['nombres' => 'Carolina','apellidos' => 'Vega',     'email' => 'carolina.vega@demo.cl',   'telefono' => '+56 9 3333 3331'],
                ['nombres' => 'Javier',  'apellidos' => 'Pizarro',  'email' => 'javier.pizarro@demo.cl',  'telefono' => '+56 9 3333 3332'],
            ],
            'Endocrinologo' => [
                ['nombres' => 'Sofía',   'apellidos' => 'Navarro',  'email' => 'sofia.navarro@demo.cl',   'telefono' => '+56 9 4444 4441'],
                ['nombres' => 'Andrés',  'apellidos' => 'León',     'email' => 'andres.leon@demo.cl',     'telefono' => '+56 9 4444 4442'],
            ],
        ];

        foreach ($pairs as $tipoNombre => $lista) {
            $tipo = TipoProfesional::whereRaw('LOWER(nombre) = ?', [mb_strtolower($tipoNombre)])->first();
            if (!$tipo) {
                $this->command->warn("⚠️  TipoProfesional '{$tipoNombre}' no encontrado. Omitiendo sus profesionales.");
                continue;
            }

            foreach ($lista as $p) {
                Profesional::firstOrCreate(
                    ['email' => $p['email']],
                    [
                        'idempresa'           => $idempresa,
                        'idsucursal'          => $tipo->idsucursal,        // coherente con el tipo
                        'tipo_profesional_id' => $tipo->id,
                        'nombres'             => $p['nombres'],
                        'apellidos'           => $p['apellidos'] ?? null,
                        'rut'                 => null,
                        'telefono'            => $p['telefono'] ?? null,
                        'email'               => $p['email'] ?? null,
                        'notas'               => null,
                    ]
                );
            }
        }

        $this->command->info('✅ Profesionales creados.');
    }
}
