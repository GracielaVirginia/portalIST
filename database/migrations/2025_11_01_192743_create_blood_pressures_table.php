<?php

// database/migrations/2025_11_01_000001_create_blood_pressures_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('blood_pressures', function (Blueprint $table) {
      $table->id();
      $table->foreignId('user_id')->constrained()->cascadeOnDelete();
      $table->date('fecha');
      $table->unsignedSmallInteger('sistolica');
      $table->unsignedSmallInteger('diastolica');
      $table->string('nota', 255)->nullable();
      $table->timestamps();
      $table->unique(['user_id','fecha']); // 1 registro por día (opcional)
    });
  }
  public function down(): void { Schema::dropIfExists('blood_pressures'); }
};
