<?php
// database/migrations/2025_10_31_000000_create_promociones_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('promociones', function (Blueprint $table) {
            $table->id();
            $table->string('titulo', 160);
            $table->string('subtitulo', 200)->nullable();
            $table->text('contenido_html')->nullable();
            $table->string('imagen_path')->nullable();      // ej: storage/app/public/...
            $table->string('cta_texto', 60)->default('Ver promociones');
            $table->string('cta_url')->nullable();          // ruta interna o externa
            $table->boolean('activo')->default(true)->index();
            $table->boolean('destacada')->default(false)->index(); // <-- clave para el banner
            $table->unsignedSmallInteger('orden')->default(0)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promociones');
    }
};
