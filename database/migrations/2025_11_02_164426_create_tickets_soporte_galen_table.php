<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets_soporte_galen', function (Blueprint $table) {
            $table->id();
            $table->string('email', 150);
            $table->string('rut', 20);
            $table->string('telefono', 30)->nullable();
            $table->text('detalle');
            $table->string('archivo')->nullable(); // ruta del archivo subido (opcional)
            $table->string('estado', 30)->default('pendiente'); // pendiente, resuelto, etc.
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets_soporte_galen');
    }
};
