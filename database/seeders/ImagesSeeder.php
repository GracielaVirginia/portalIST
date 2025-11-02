<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ImagesSeeder extends Seeder
{
    public function run(): void
    {
        $imagenes = [];

        for ($i = 1; $i <= 7; $i++) {
            $imagenes[] = [
                'nombre' => "{$i}.png",
                'seleccionada' => $i === 6, // ← Imagen 6 marcada como seleccionada
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('images')->insert($imagenes);
    }
}
