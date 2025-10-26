<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('examen_nombre', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20);
            $table->string('nombre', 100);
            $table->string('tipo', 20); // IMAGEN, LAB, PROCEDIMIENTO, CONSULTA
            $table->foreignId('especialidad_id')->constrained('especialidad');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('examen_nombre');
    }
};
