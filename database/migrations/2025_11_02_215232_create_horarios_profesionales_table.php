<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('horarios_profesionales', function (Blueprint $table) {
            $table->id();

            // Relaciones base
            $table->unsignedBigInteger('idempresa')->nullable();
            $table->unsignedBigInteger('idprofesional')->comment('ID del usuario médico');

            // Configuración del horario
            $table->string('tipo', 50)->nullable()->comment('Tipo de jornada o turno');
            $table->string('dia_semana', 20)->comment('lunes, martes, etc.');
            $table->time('hora_inicio')->comment('Hora de inicio laboral');
            $table->time('hora_fin')->comment('Hora de término laboral');
            $table->integer('duracion_bloque')->default(30)->comment('Duración de cada bloque en minutos');

            // Firma (opcional)
            $table->string('firma')->nullable()->comment('Firma digital del profesional');

            $table->timestamps();

            // Claves foráneas
            $table->foreign('idprofesional')->references('id')->on('users')->onDelete('cascade');
            $table->index(['idprofesional', 'dia_semana']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('horarios_profesionales');
    }
};
