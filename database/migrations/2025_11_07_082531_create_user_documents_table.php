<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_documents', function (Blueprint $table) {
            $table->id();

            // === Siempre: el propietario del documento (paciente/usuario) ===
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->cascadeOnDelete(); // Al borrar usuario: elimina registros (luego borraremos archivo en evento Eloquent)

            // === Opcional: vínculo a una cita/atención concreta ===
            // Si el doc es "historial general", este campo quedará NULL.
            $table->foreignId('cita_id')
                ->nullable()
                ->constrained('citas')
                ->cascadeOnUpdate()
                // Elige UNA de estas dos políticas:
                // ->nullOnDelete();   // (recomendada) Si borran la cita, el doc queda desasociado pero NO se pierde
                ->cascadeOnDelete();   // (alternativa) Si borran la cita, también se borra el doc

            // === Metadatos del archivo ===
            $table->string('disk', 50)->default('public');      // p.ej. public, s3
            $table->string('path', 2048);                       // ruta interna en el disk (users/{id}/docs/xxx.pdf)
            $table->string('original_name', 512);               // nombre original
            $table->string('mime_type', 255)->nullable();       // application/pdf, image/png, etc.
            $table->unsignedBigInteger('size')->nullable();     // bytes
            $table->string('hash_md5', 64)->nullable();         // opcional, para verificación/dedupe

            // === Clasificación y control ===
            $table->string('category', 100)->nullable();        // p.ej. "examen", "certificado", "receta", etc.
            $table->string('label', 150)->nullable();           // título visible opcional
            $table->text('description')->nullable();            // notas del paciente
            $table->json('meta')->nullable();                   // extras: fechas, etiquetas, origen, etc.

            // (Opcional) flujo de revisión por profesional
            $table->boolean('is_reviewed')->default(false);
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();

            $table->timestamps();
            // $table->softDeletes(); // Descomenta si quieres “papelera” para documentos

            // === Índices útiles ===
            $table->index(['user_id', 'created_at'], 'ud_user_created_idx');
            $table->index(['cita_id', 'created_at'], 'ud_cita_created_idx');
            $table->index(['category'], 'ud_category_idx');
            $table->index(['mime_type'], 'ud_mime_idx');
        });

        // (Opcional) Si tu motor soporta CHECKs y quieres validar tamaño > 0 cuando existe
        $driver = Schema::getConnection()->getDriverName();
        if (in_array($driver, ['mysql', 'pgsql'])) {
            try {
                DB::statement("ALTER TABLE user_documents
                    ADD CONSTRAINT chk_user_documents_size
                    CHECK (size IS NULL OR size >= 0)");
            } catch (\Throwable $e) {
                // silenciar si no se soporta (p.ej. MariaDB viejas)
            }
        }
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();
        if (in_array($driver, ['mysql', 'pgsql'])) {
            try {
                DB::statement("ALTER TABLE user_documents DROP CONSTRAINT chk_user_documents_size");
            } catch (\Throwable $e) {
                try { DB::statement("ALTER TABLE user_documents DROP CHECK chk_user_documents_size"); } catch (\Throwable $e) {}
            }
        }

        Schema::dropIfExists('user_documents');
    }
};
