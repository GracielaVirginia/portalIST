<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('citas', function (Blueprint $t) {
            $t->id();

            $t->unsignedBigInteger('idempresa')->default(1);
            $t->unsignedBigInteger('idsucursal')->nullable();

            // Relación con profesionales (NO users)
            $t->foreignId('profesional_id')
              ->constrained('profesionales')
              ->onDelete('cascade');

            // Si tienes tabla pacientes, luego puedes apuntar aquí
            $t->unsignedBigInteger('paciente_id')->nullable();

            $t->date('fecha');
            $t->time('hora_inicio');
            $t->time('hora_fin');

            $t->string('tipo_atencion', 20)->default('presencial'); // presencial|remota
            $t->string('estado', 20)->default('pendiente');         // pendiente|confirmada|atendida|cancelada
            $t->text('motivo')->nullable();

            $t->timestamps();

            $t->index(['profesional_id','fecha']);
            $t->index(['fecha','hora_inicio','hora_fin']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('citas');
    }
};
