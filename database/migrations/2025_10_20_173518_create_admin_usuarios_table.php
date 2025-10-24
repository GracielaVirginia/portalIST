<?php

// database/migrations/xxxx_create_admin_usuarios_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('admin_usuarios', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_completo', 150);
            $table->string('email', 100)->unique();
            $table->string('rut', 100)->unique();
            $table->string('user', 100)->unique();
            $table->string('rol', 30);
            $table->string('especialidad', 100)->nullable();
            $table->string('password_hash', 255);
            $table->boolean('activo')->default(true);
            $table->timestamp('ultimo_login')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();
            
        });
    }

    public function down()
    {
        Schema::dropIfExists('admin_usuarios');
    }
};