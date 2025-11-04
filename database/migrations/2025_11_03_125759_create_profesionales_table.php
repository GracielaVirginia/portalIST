<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('profesionales', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('idempresa')->default(1);

            // Dependencias
            $table->unsignedBigInteger('idsucursal');
            $table->unsignedBigInteger('tipo_profesional_id');

            // Datos del profesional
            $table->string('nombres', 120);
            $table->string('apellidos', 120)->nullable();
            $table->string('rut', 30)->nullable();
            $table->string('telefono', 60)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('notas', 255)->nullable();

            $table->timestamps();

            // FKs
            $table->foreign('idsucursal')->references('id')->on('sucursales')->onDelete('cascade');
            $table->foreign('tipo_profesional_id')->references('id')->on('tipos_profesionales')->onDelete('restrict');

            // Índices útiles
            $table->index(['idsucursal', 'tipo_profesional_id']);
            $table->index('nombres');
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profesionales');
    }
};
