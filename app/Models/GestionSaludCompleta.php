<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GestionSaludCompleta extends Model
{
    use HasFactory;

    protected $table = 'gestiones_salud_completa';
    protected $primaryKey = 'id';
    public $timestamps = false; // la migration usa timestamps manuales con useCurrent()

    protected $fillable = [
        // Paciente
        'tipo_documento',
        'numero_documento',
        'nombre_paciente',
        'fecha_nacimiento',
        'sexo',
        'genero',
        'telefono',
        'email',
        'direccion',
        'grupo_sanguineo',
        'alergias_conocidas',

        // Preferencias
        'notificaciones_email',
        'notificaciones_sms',
        'notificaciones_app',
        'idioma_preferido',

        // Solicitud
        'origen_solicitud',
        'tipo_gestion',
        'especialidad',
        'tipo_examen',
        'examen_codigo',
        'examen_nombre',
        'fecha_solicitud',
        'fecha_cita_programada',
        'lugar_cita',
        'estado_solicitud',
        'motivo_rechazo',
        'usuario_responsable',

        // Atención
        'id_profesional',
        'tipo_atencion',
        'modalidad_atencion',
        'fecha_atencion',
        'duracion_minutos',
        'estado_asistencia',
        'motivo_no_asistencia',

        // Clínico
        'motivo_consulta',
        'anamnesis',
        'antecedentes_personales',
        'antecedentes_familiares',
        'factores_riesgo',
        'examen_fisico',
        'diagnostico_principal',
        'diagnosticos_secundarios',
        'impresion_diagnostica',
        'plan_de_accion',
        'resumen_atencion',
        'seguimiento_requerido',
        'fecha_proximo_control',

        // Signos vitales
        'peso_kg',
        'talla_cm',
        'imc',
        'presion_arterial',
        'frecuencia_cardiaca',
        'saturacion_oxigeno',
        'temperatura',
        'circunferencia_abdominal',

        // Medicamentos
        'medicamentos_activos',
        'alergias_medicamentosas',
        'interacciones_conocidas',

        // Crónicas
        'tiene_diabetes',
        'tipo_diabetes',
        'ultima_hba1c',
        'fecha_ultima_hba1c',
        'tiene_hta',
        'tiene_asma',
        'tiene_erc',

        // Recordatorios
        'recordatorios_activos',
        'controles_programados',
        'fecha_proximo_control_glucosa',
        'fecha_proxima_vacuna',
        'frecuencia_control_hta',

        // Receta
        'tiene_receta',
        'detalle_receta',

        // Licencia
        'tiene_licencia',
        'tipo_licencia',
        'dias_licencia',
        'fecha_inicio_licencia',
        'fecha_fin_licencia',
        'justificacion_licencia',

        // Informe
        'tiene_informe',
        'contenido_informe',

        // PDFs
        'url_pdf_receta',
        'url_pdf_licencia',
        'url_pdf_informe',

        // Seguridad
        'consentimiento_informado',
        'nivel_urgencia',
        'cobertura_validada',

        // Auditoría
        'firma_profesional_hash',
        'ip_registro',
        'usuario_registro',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'fecha_solicitud' => 'datetime',
        'fecha_cita_programada' => 'datetime',
        'fecha_atencion' => 'datetime',
        'fecha_proximo_control' => 'date',
        'fecha_ultima_hba1c' => 'date',
        'fecha_proximo_control_glucosa' => 'date',
        'fecha_proxima_vacuna' => 'date',
        'fecha_inicio_licencia' => 'date',
        'fecha_fin_licencia' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',

        'notificaciones_email' => 'boolean',
        'notificaciones_sms' => 'boolean',
        'notificaciones_app' => 'boolean',
        'seguimiento_requerido' => 'boolean',
        'tiene_diabetes' => 'boolean',
        'tiene_hta' => 'boolean',
        'tiene_asma' => 'boolean',
        'tiene_erc' => 'boolean',
        'tiene_receta' => 'boolean',
        'tiene_licencia' => 'boolean',
        'tiene_informe' => 'boolean',
        'consentimiento_informado' => 'boolean',
        'cobertura_validada' => 'boolean',
    ];

    // Relación sugerida (no estrictamente necesaria): un rut puede tener varios registros
    public function scopePorRut($query, string $rut)
    {
        return $query->where('tipo_documento', 'RUT')
            ->where('numero_documento', $rut);
    }
}
