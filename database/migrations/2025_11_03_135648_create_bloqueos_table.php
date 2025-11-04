<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bloqueos', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('idempresa')->default(1);
            $table->unsignedBigInteger('idsucursal');      // coherente con el profesional
            $table->unsignedBigInteger('profesional_id');  // obligatorio

            // Asociar (opcionalmente) a un horario concreto
            $table->unsignedBigInteger('horario_id')->nullable();

            // Modalidad del bloqueo:
            // - puntual por fecha  -> fecha != null, dia_semana = null
            // - recurrente semanal -> fecha = null, dia_semana != null
            $table->date('fecha')->nullable();
            $table->string('dia_semana', 20)->nullable();   // lunes, martes, ... si es recurrente

            $table->time('inicio');                         // HH:MM
            $table->integer('duracion');                    // minutos
            $table->string('motivo', 120)->nullable();

            $table->timestamps();

            $table->foreign('idsucursal')->references('id')->on('sucursales')->onDelete('cascade');
            $table->foreign('profesional_id')->references('id')->on('profesionales')->onDelete('cascade');
            $table->foreign('horario_id')->references('id')->on('horarios')->onDelete('cascade');

            $table->index(['profesional_id','fecha']);
            $table->index(['profesional_id','dia_semana']);
            $table->index(['horario_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bloqueos');
    }
};
