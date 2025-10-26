<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OrigenSolicitud;

class OrigenSolicitudSeeder extends Seeder
{
    public function run()
    {
        $origenes = [
            ['name' => 'CALLCENTER'],
            ['name' => 'APP'],
            ['name' => 'WEB'],
            ['name' => 'PRESENCIAL'],
        ];

        foreach ($origenes as $origen) {
            OrigenSolicitud::create($origen);
        }
    }
}
