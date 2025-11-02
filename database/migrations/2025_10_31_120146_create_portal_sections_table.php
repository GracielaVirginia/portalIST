<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portal_sections', function (Blueprint $table) {
            $table->id();

            // A qué "página" pertenece este bloque (ej: 'conoce-mas')
            $table->string('page_slug', 50)->index();

            // Tipo de bloque (ej: 'hero', 'beneficios', 'como_funciona', 'novedades', 'testimonios', 'kpis', 'seguridad')
            $table->string('tipo', 50)->index();

            // Título/subtítulo opcionales (cabeceras del bloque)
            $table->string('titulo', 255)->nullable();
            $table->string('subtitulo', 255)->nullable();

            // Contenido flexible en JSON (estructura varía según tipo)
            $table->json('contenido')->nullable();

            // Orden de aparición y visibilidad
            $table->unsignedSmallInteger('posicion')->default(1)->index();
            $table->boolean('visible')->default(true)->index();

            // Publicación programada (opcional)
            $table->timestamp('publicar_desde')->nullable()->index();
            $table->timestamp('publicar_hasta')->nullable()->index();

            // Auditoría
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();

            // Índices y claves
            $table->foreign('updated_by')->references('id')->on('admin_usuarios')->onDelete('set null');
            $table->unique(['page_slug', 'tipo', 'posicion'], 'uniq_page_tipo_posicion');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portal_sections');
    }
};
