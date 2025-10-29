<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('email_verification_requests', function (Blueprint $table) {
            $table->id();
            $table->string('email', 190)->index();
            $table->string('code', 6)->index();
            $table->enum('status', ['pending','sent','failed','verified','expired'])->default('pending')->index();
            $table->unsignedTinyInteger('attempts')->default(0); // reintentos de envío
            $table->text('last_error')->nullable();
            $table->string('ip', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps(); // created_at = cuando se generó el código
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_verification_requests');
    }
};
