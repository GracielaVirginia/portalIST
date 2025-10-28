<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('tickets_soporte', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('rut', 20);
            $table->string('telefono', 30)->nullable();
            $table->text('detalle');
            $table->string('archivo')->nullable(); // ruta del adjunto si aplica
            $table->string('estado', 30)->default('pendiente');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tickets_soporte');
    }
};
