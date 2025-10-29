<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('assistant_rules', function (Blueprint $table) {
            $table->id();
            $table->string('title', 150);               // identificador visible en admin
            $table->text('keywords');                   // coma o salto de línea separadas; o patrón regex
            $table->boolean('use_regex')->default(false); // si true, 'keywords' es patrón(s) regex (uno por línea)
            $table->text('response');                   // HTML o texto plano
            $table->enum('match_mode', ['any','all'])->default('any'); // coincide si cualquiera o todas las palabras
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->nullable();  // prioridad
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('assistant_rules');
    }
};
