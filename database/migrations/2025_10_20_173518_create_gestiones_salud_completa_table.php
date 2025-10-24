<?php

// database/migrations/xxxx_create_gestiones_salud_completa_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('gestiones_salud_completa', function (Blueprint $table) {
            $table->id();
            // 👤 Paciente
            $table->string('tipo_documento', 10);
            $table->string('numero_documento', 20);
            $table->string('nombre_paciente', 150);
            $table->date('fecha_nacimiento');
            $table->char('sexo', 1)->nullable();
            $table->string('genero', 30)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('direccion', 255)->nullable();
            $table->string('grupo_sanguineo', 10)->nullable();
            $table->text('alergias_conocidas')->nullable();

            // 📲 Preferencias
            $table->boolean('notificaciones_email')->default(true);
            $table->boolean('notificaciones_sms')->default(true);
            $table->boolean('notificaciones_app')->default(true);
            $table->string('idioma_preferido', 10)->default('es');

            // 📞 Solicitud
            $table->string('origen_solicitud', 30);
            $table->string('tipo_gestion', 20)->default('CITA');
            $table->string('especialidad', 100)->nullable();
            $table->string('tipo_examen', 20)->nullable();
            $table->string('examen_codigo', 30)->nullable();
            $table->string('examen_nombre', 150)->nullable();
            $table->timestamp('fecha_solicitud')->useCurrent();
            $table->timestamp('fecha_cita_programada')->nullable();
            $table->string('lugar_cita', 150)->nullable();
            $table->string('estado_solicitud', 20)->default('PENDIENTE');
            $table->string('motivo_rechazo', 200)->nullable();
            $table->string('usuario_responsable', 50)->nullable();
            $table->string('numero_caso', 50)->nullable();

            // 🩺 Atención
            $table->unsignedInteger('id_profesional')->nullable();
            $table->string('tipo_atencion', 20)->nullable()->default('CONTROL');
            $table->string('modalidad_atencion', 20)->nullable()->default('PRESENCIAL');
            $table->timestamp('fecha_atencion')->nullable();
            $table->integer('duracion_minutos')->nullable();
            $table->string('estado_asistencia', 20)->nullable()->default('NO_REALIZADA');
            $table->string('motivo_no_asistencia', 100)->nullable();

            // 📝 Clínico
            $table->text('motivo_consulta')->nullable();
            $table->text('anamnesis')->nullable();
            $table->text('antecedentes_personales')->nullable();
            $table->text('antecedentes_familiares')->nullable();
            $table->text('factores_riesgo')->nullable();
            $table->text('examen_fisico')->nullable();
            $table->string('diagnostico_principal', 200)->nullable();
            $table->text('diagnosticos_secundarios')->nullable();
            $table->text('impresion_diagnostica')->nullable();
            $table->text('plan_de_accion')->nullable();
            $table->text('resumen_atencion')->nullable();
            $table->boolean('seguimiento_requerido')->nullable()->default(false);
            $table->date('fecha_proximo_control')->nullable();

            // 🔬 Signos vitales
            $table->decimal('peso_kg', 5, 2)->nullable();
            $table->integer('talla_cm')->nullable();
            $table->decimal('imc', 4, 1)->nullable();
            $table->string('presion_arterial', 10)->nullable();
            $table->integer('frecuencia_cardiaca')->nullable();
            $table->integer('saturacion_oxigeno')->nullable();
            $table->decimal('temperatura', 3, 1)->nullable();
            $table->integer('circunferencia_abdominal')->nullable();

            // 💊 Medicamentos
            $table->text('medicamentos_activos')->nullable();
            $table->text('alergias_medicamentosas')->nullable();
            $table->text('interacciones_conocidas')->nullable();

            // 📈 Condiciones crónicas
            $table->boolean('tiene_diabetes')->default(false);
            $table->string('tipo_diabetes', 10)->nullable();
            $table->decimal('ultima_hba1c', 3, 1)->nullable();
            $table->date('fecha_ultima_hba1c')->nullable();
            $table->boolean('tiene_hta')->default(false);
            $table->boolean('tiene_asma')->default(false);
            $table->boolean('tiene_erc')->default(false);

            // ⏰ Recordatorios
            $table->text('recordatorios_activos')->nullable();
            $table->text('controles_programados')->nullable();
            $table->date('fecha_proximo_control_glucosa')->nullable();
            $table->date('fecha_proxima_vacuna')->nullable();
            $table->string('frecuencia_control_hta', 20)->nullable();

            // 💊 Receta
            $table->boolean('tiene_receta')->default(false);
            $table->text('detalle_receta')->nullable();

            // 📜 Licencia
            $table->boolean('tiene_licencia')->default(false);
            $table->string('tipo_licencia', 20)->nullable();
            $table->integer('dias_licencia')->nullable();
            $table->date('fecha_inicio_licencia')->nullable();
            $table->date('fecha_fin_licencia')->nullable();
            $table->text('justificacion_licencia')->nullable();

            // 📄 Informe
            $table->boolean('tiene_informe')->default(false);
            $table->text('contenido_informe')->nullable();

            // 🖨️ PDFs
            $table->string('url_pdf_receta', 255)->nullable();
            $table->string('url_pdf_licencia', 255)->nullable();
            $table->string('url_pdf_informe', 255)->nullable();

            // ✅ Seguridad
            $table->boolean('consentimiento_informado')->default(false);
            $table->string('nivel_urgencia', 10)->default('BAJO');
            $table->boolean('cobertura_validada')->default(false);

            // 🔐 Auditoría
            $table->string('firma_profesional_hash', 255)->nullable();
            $table->string('ip_registro', 45)->nullable();
            $table->string('usuario_registro', 50)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();
            //flags para envio correo
            $table->boolean('email_route')->default(false);
            $table->boolean('OTP')->default(false);
            $table->boolean('remember')->default(false);
            $table->boolean('flag')->default(false);

            // Índices
            $table->index(['tipo_documento', 'numero_documento']);
            $table->index('estado_solicitud');
            $table->index('fecha_atencion');
            $table->index('origen_solicitud');
            $table->index('email');
        });
    }

    public function down()
    {
        Schema::dropIfExists('gestiones_salud_completa');
    }
};
