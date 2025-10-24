<?php

// database/migrations/xxxx_create_system_logs_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('system_logs', function (Blueprint $table) {
            $table->id();
            $table->string('tipo_accion', 50);
            $table->unsignedBigInteger('id_gestion')->nullable();
            $table->unsignedBigInteger('id_usuario')->nullable();
            $table->text('detalles')->nullable();
            $table->string('ip_origen', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('tipo_accion');
            $table->index('id_gestion');
            $table->index('id_usuario');
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('system_logs');
    }
};