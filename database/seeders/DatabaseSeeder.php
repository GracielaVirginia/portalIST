<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
public function run(): void
{
    $this->call([
        GestionesSaludCompletaSeeder::class,
        AdminUsuarioSeeder::class,
        NoticiasTableSeeder::class,
        OrigenSolicitudSeeder::class,
        TipoGestionSeeder::class,
        EspecialidadSeeder::class,
        ExamenNombreSeeder::class,
        PortalValidacionConfigSeeder::class,
        SystemSettingsSeeder::class,
        ImagesSeeder::class,
        PortalSectionsSeeder::class,
        PromocionesSeeder::class,
        ]);
}

}
