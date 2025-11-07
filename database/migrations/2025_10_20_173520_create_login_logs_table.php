<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('login_log', function (Blueprint $table) {
            $table->id();

            // Relación con usuarios
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Información del acceso
            $table->string('ip_address', 64)->nullable();
            $table->text('user_agent')->nullable();

            // Sesión
            $table->string('session_id', 100)->nullable()->index();     // para identificar sesión
            $table->timestamp('logged_in_at')->useCurrent();             // inicio
            $table->timestamp('last_seen_at')->nullable();               // última actividad
            $table->timestamp('logged_out_at')->nullable();              // cierre (logout o timeout)
            $table->unsignedInteger('duration_seconds')->nullable();     // duración total
            $table->string('close_reason', 30)->nullable();              // logout | timeout

            // Marcas de tiempo
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('login_log');
    }
};
