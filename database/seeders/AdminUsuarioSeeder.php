<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\AdminUsuario;

class AdminUsuarioSeeder extends Seeder
{
    public function run(): void
    {
        AdminUsuario::create([
            'nombre_completo' => 'Administrador General',
            'email'           => 'admin@sistema.local',
            'rut'             => '11111111-1',
            'user'            => 'Administrador',
            'rol'             => 'Administrador',
            'especialidad'    => null,
            'password_hash'   => Hash::make('Administrador123'),
            'activo'          => true,
        ]);
    }
}
