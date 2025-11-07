<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('other_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('session_timeout')->default(20); // minutos de inactividad
            $table->string('font_family')->default('Inter'); // fuente general del portal
            $table->string('theme_color')->default('#7c3aed'); // color principal (violeta)
            $table->boolean('dark_mode_default')->default(false); // modo oscuro por defecto
            $table->boolean('animations_enabled')->default(true); // permite animaciones suaves
            $table->boolean('show_tips')->default(true); // mostrar ayudas o tours
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('other_settings');
    }
};
