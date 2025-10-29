<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PortalValidacionConfigSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('portal_validacion_config')->truncate();

        DB::table('portal_validacion_config')->insert([
            [
                'nombre' => 'Sin validación',
                'slug' => 'sin-validacion',
                'descripcion' => 'Acceso directo al portal sin pasos adicionales.',
                'imagen' => 'images/validaciones/sin-validacion.jpg',
                'activo' => false,
            ],
            [
                'nombre' => 'Con número de caso',
                'slug' => 'numero-caso',
                'descripcion' => 'Validación ingresando un número de caso asignado.',
                'imagen' => 'images/validaciones/numero-caso.jpg',
                'activo' => true, // ejemplo: activa por defecto
            ],
            [
                'nombre' => 'Tres opciones (celular, email o examen)',
                'slug' => 'tres-opciones',
                'descripcion' => 'Validación mediante celular, correo electrónico o un examen reciente.',
                'imagen' => 'images/validaciones/tres-opciones.jpg',
                'activo' => false,
            ],
            [
                'nombre' => 'Creando cuenta',
                'slug' => 'creando-cuenta',
                'descripcion' => 'Registro de cuenta nueva con credenciales propias.',
                'imagen' => 'images/validaciones/creando-cuenta.jpg',
                'activo' => false,
            ],
        ]);
    }
}
