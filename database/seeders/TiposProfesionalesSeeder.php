<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TipoProfesional;

class TiposProfesionalesSeeder extends Seeder
{
    public function run(): void
    {
        $idempresa  = 1;
        $idsucursal = 1;

        $items = [
            [
                'nombre'      => 'Nutricionista',
                'slug'        => 'nutricionista',
                'descripcion' => 'Especialista en nutrición clínica.',
                'visible'     => true,
                'orden'       => 1,
            ],
            [
                'nombre'      => 'Ginecólogo',
                'slug'        => 'ginecologo',
                'descripcion' => 'Salud reproductiva femenina.',
                'visible'     => true,
                'orden'       => 2,
            ],
            [
                'nombre'      => 'Psicólogo',
                'slug'        => 'psicologo',
                'descripcion' => 'Salud mental y terapia psicológica.',
                'visible'     => true,
                'orden'       => 3,
            ],
            [
                'nombre'      => 'Endocrinólogo',
                'slug'        => 'endocrinologo',
                'descripcion' => 'Trastornos hormonales y metabólicos.',
                'visible'     => true,
                'orden'       => 4,
            ],
        ];

        foreach ($items as $item) {
            TipoProfesional::firstOrCreate(
                ['slug' => $item['slug']],
                $item + ['idempresa'=>$idempresa, 'idsucursal'=>$idsucursal]
            );
        }
    }
}
