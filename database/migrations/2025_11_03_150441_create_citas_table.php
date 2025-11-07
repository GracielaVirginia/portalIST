<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('citas', function (Blueprint $table) {
            $table->id();

            // Si no tienes tablas empresas/sucursales, déjalos como unsigned + index
            $table->unsignedBigInteger('idempresa')->nullable()->index();
            $table->unsignedBigInteger('idsucursal')->nullable()->index();

            // Profesional (ajusta el nombre de la tabla si no es 'profesionales')
            $table->foreignId('profesional_id')
                ->constrained('profesionales')
                ->cascadeOnUpdate()
                ->restrictOnDelete(); // cambia a ->cascadeOnDelete() si quieres borrar citas al eliminar profesional

            // Paciente: referencia a users con BORRADO EN CASCADA (clave del problema 1451)
            $table->foreignId('paciente_id')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            // Datos de la cita
            $table->date('fecha');
            $table->time('hora_inicio');
            $table->time('hora_fin');

            $table->enum('tipo_atencion', ['presencial', 'remota'])->default('presencial');
            $table->string('estado', 20)->default('pendiente'); // pendiente|confirmada|cancelada, etc.
            $table->string('lugar_cita', 60)->comment('De dónde es el Paciente');
            $table->text('motivo')->nullable();

            $table->timestamps();
            // $table->softDeletes(); // <-- descomenta si quieres SoftDeletes

            // Evita duplicar el mismo bloque para el mismo profesional
            $table->unique(['profesional_id', 'fecha', 'hora_inicio', 'hora_fin'], 'cita_slot_unico');
            $table->index(['profesional_id', 'fecha'], 'cita_profesional_fecha_idx');
            $table->index(['paciente_id', 'fecha'], 'cita_paciente_fecha_idx');
        });

        // (Opcional) CHECK de integridad si tu motor lo soporta (MySQL 8+ / MariaDB 10.2+ / PostgreSQL)
        $driver = Schema::getConnection()->getDriverName();
        if (in_array($driver, ['mysql', 'pgsql'])) {
            try {
                DB::statement('ALTER TABLE citas ADD CONSTRAINT chk_citas_horas CHECK (hora_fin > hora_inicio)');
            } catch (\Throwable $e) {
                // Lo omitimos silenciosamente si no es soportado o si es MariaDB con sintaxis distinta
            }
        }
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();
        if (in_array($driver, ['mysql', 'pgsql'])) {
            try {
                DB::statement('ALTER TABLE citas DROP CONSTRAINT chk_citas_horas');
            } catch (\Throwable $e) {
                try { DB::statement('ALTER TABLE citas DROP CHECK chk_citas_horas'); } catch (\Throwable $e) {}
            }
        }

        Schema::dropIfExists('citas');
    }
};
