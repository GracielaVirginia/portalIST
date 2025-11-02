<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('weight_entries', function (Blueprint $table) {
      $table->id();
      $table->foreignId('user_id')->constrained()->cascadeOnDelete();
      $table->date('fecha');
      $table->decimal('valor', 5, 1); // kg con 1 decimal
      $table->string('nota', 255)->nullable();
      $table->timestamps();
      $table->unique(['user_id','fecha']); // opcional
    });
  }
  public function down(): void { Schema::dropIfExists('weight_entries'); }
};
