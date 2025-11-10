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

            // Usuario autenticado (si aplica)
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            // Documento con el que intentó (RUT / Pasaporte / Email, etc.)
            $table->string('login_input', 100)->nullable()->index();

            // Técnica
            $table->string('ip_address', 45)->nullable()->index(); // IPv4/IPv6
            $table->string('user_agent', 255)->nullable();

            /**
             * Resultado del evento:
             * - visit
             * - verify_found | verify_not_found
             * - invalid_password | user_not_found | blocked
             * - success  (login OK, antes de validación)
             * - validation_failed | validation_blocked
             * - portal_access (llegó a portal.home)
             */
            $table->string('outcome', 50)->index();

            // Correlativo del intento (útil para auditoría)
            $table->unsignedInteger('attempt_number')->default(1);

            // Bloqueo (login o validación)
            $table->boolean('is_blocked')->default(false);
            $table->timestamp('blocked_at')->nullable();

            $table->timestamps();

            // Índices compuestos típicos de consulta
            $table->index(['login_input', 'created_at']);
            $table->index(['outcome', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('login_attempts');
    }
};
