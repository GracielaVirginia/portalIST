<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sucursales', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('idempresa')->nullable();

            $table->string('nombre', 160)->unique();
            $table->string('slug', 180)->unique();
            $table->string('descripcion', 255)->nullable();

            // Datos opcionales útiles
            $table->string('direccion', 200)->nullable();
            $table->string('ciudad', 100)->nullable();
            $table->string('region', 100)->nullable();
            $table->string('telefono', 60)->nullable();
            $table->string('email', 160)->nullable();

            $table->boolean('visible')->default(true);
            $table->unsignedInteger('orden')->default(0);
            $table->timestamps();

            $table->index(['idempresa']);
            $table->index(['visible', 'orden']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sucursales');
    }
};
