<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('lugar_cita', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100)->unique();
            $table->string('direccion', 200)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('lugar_cita');
    }
};
