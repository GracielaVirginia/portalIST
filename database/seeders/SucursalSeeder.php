<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sucursal;

class SucursalSeeder extends Seeder
{
    public function run(): void
    {
        $idempresa = 1;

        $items = [
            [
                'nombre'      => 'Clínica Centro',
                'slug'        => 'clinica-centro',
                'descripcion' => 'Casa matriz en el centro.',
                'direccion'   => 'Av. Principal 123',
                'ciudad'      => 'Santiago',
                'region'      => 'RM',
                'telefono'    => '+56 2 2345 6789',
                'email'       => 'centro@clinica.cl',
                'visible'     => true,
                'orden'       => 1,
            ],
            [
                'nombre'      => 'Sucursal Norte',
                'slug'        => 'sucursal-norte',
                'descripcion' => 'Cobertura zona norte.',
                'direccion'   => 'Ruta 5 Norte Km 10',
                'ciudad'      => 'Antofagasta',
                'region'      => 'Antofagasta',
                'telefono'    => '+56 55 223 4455',
                'email'       => 'norte@clinica.cl',
                'visible'     => true,
                'orden'       => 2,
            ],
            [
                'nombre'      => 'Sucursal Sur',
                'slug'        => 'sucursal-sur',
                'descripcion' => 'Cobertura zona sur.',
                'direccion'   => 'Av. del Bosque 456',
                'ciudad'      => 'Concepción',
                'region'      => 'Biobío',
                'telefono'    => '+56 41 221 3344',
                'email'       => 'sur@clinica.cl',
                'visible'     => true,
                'orden'       => 3,
            ],
        ];

        foreach ($items as $item) {
            Sucursal::firstOrCreate(
                ['slug' => $item['slug']],
                $item + ['idempresa' => $idempresa]
            );
        }
    }
}
