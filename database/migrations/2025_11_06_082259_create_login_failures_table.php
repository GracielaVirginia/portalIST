<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('login_failures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('identifier')->nullable(); // email o RUT digitado
            $table->string('ip_address', 64)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('reason', 80)->nullable(); // credenciales inválidas, lockout, etc.
            $table->timestamp('occurred_at')->useCurrent();
            $table->boolean('notified')->default(false); // si ya se ofreció ayuda
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('login_failures');
    }
};
