<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tipos_profesionales', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('idempresa')->nullable();
            $table->unsignedBigInteger('idsucursal')->nullable();

            $table->string('nombre', 120)->unique();
            $table->string('slug', 140)->unique();
            $table->string('descripcion', 255)->nullable();
            $table->boolean('visible')->default(true);
            $table->unsignedInteger('orden')->default(0);

            $table->timestamps();

            $table->index(['idempresa','idsucursal']);
            $table->index(['visible', 'orden']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tipos_profesionales');
    }
};
