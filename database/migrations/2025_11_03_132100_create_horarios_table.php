<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('horarios', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('idempresa')->default(1);

            // Dependencias
            $table->unsignedBigInteger('idsucursal');       // coherente con el profesional
            $table->unsignedBigInteger('profesional_id');   // FK a profesionales.id

            // Configuración del horario (franja recurrente)
            $table->string('tipo', 50)->nullable()->comment('Turno/jornada opcional');
            $table->string('dia_semana', 20);               // lunes, martes, ...
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->integer('duracion_bloque')->default(30); // minutos (para segmentación)

            $table->timestamps();

            // FKs
            $table->foreign('idsucursal')->references('id')->on('sucursales')->onDelete('cascade');
            $table->foreign('profesional_id')->references('id')->on('profesionales')->onDelete('cascade');

            // Índices útiles
            $table->index(['profesional_id', 'dia_semana']);
            $table->index(['idsucursal', 'dia_semana']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('horarios');
    }
};
