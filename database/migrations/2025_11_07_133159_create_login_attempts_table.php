<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('login_attempts', function (Blueprint $table) {
            $table->id();

            // Si se identificó a qué usuario corresponde el RUT/email, se llena; si no, queda NULL
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete(); // si borras el usuario, conservamos el intento

            // Lo que el usuario escribió en el campo de login (RUT, email, etc.)
            $table->string('login_input', 120)->index();

            // Metadatos de la solicitud
            $table->string('ip_address', 45)->index();    // IPv4/IPv6
            $table->string('user_agent', 255)->nullable();

            // Resultado del intento
            $table->enum('outcome', [
                'success',          // autenticación correcta
                'user_not_found',   // no existe paciente/usuario para ese login_input
                'invalid_password', // usuario encontrado pero clave incorrecta
                'blocked',          // intento rechazado por bloqueo (rate limit / política)
            ])->index();

            // Contador del intento (útil para auditoría: intento #N para ese login_input en la ventana que definas)
            $table->unsignedSmallInteger('attempt_number')->default(1);

            // Estado de bloqueo en el momento del intento
            $table->boolean('is_blocked')->default(false)->index();
            $table->timestamp('blocked_at')->nullable();

            $table->timestamps(); // created_at = fecha del intento; updated_at por si ajustas algo luego

            // Índices útiles para consultas típicas
            $table->index(['login_input', 'created_at'], 'attempts_login_created_idx');
            $table->index(['user_id', 'created_at'], 'attempts_user_created_idx');
            $table->index(['ip_address', 'created_at'], 'attempts_ip_created_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('login_attempts');
    }
};
