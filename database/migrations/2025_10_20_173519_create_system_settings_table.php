<?php

// database/migrations/xxxx_create_system_settings_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('clave', 50)->unique();
            $table->text('valor');
            $table->string('descripcion', 255)->nullable();
            $table->string('tipo', 20)->default('string');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();
            $table->foreign('updated_by')->references('id')->on('admin_usuarios')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('system_settings');
    }
};