<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('alertas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('tipo', 50); // 'login_fallido', 'validacion_fallida', 'usuario_bloqueado'
            $table->unsignedInteger('intentos')->default(0);
            $table->string('ip', 45)->nullable();
            $table->string('user_agent', 1024)->nullable();
            $table->string('documento')->nullable(); // rut/pasaporte/email usado en el intento
            $table->json('extra')->nullable();
            $table->timestamp('ocurrio_en')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('alertas');
    }
};
