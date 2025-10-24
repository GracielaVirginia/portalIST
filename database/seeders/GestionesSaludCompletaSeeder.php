<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GestionSaludCompleta;
use Illuminate\Support\Str;
use Carbon\Carbon;

class GestionesSaludCompletaSeeder extends Seeder
{
    public function run(): void
    {
        $rutList = [
            '11111111-1',
            '22222222-2',
            '33333333-3',
            '44444444-4',
            '55555555-5',
            '66666666-6',
            '77777777-7',
            '88888888-8',
            '99999999-9',
            '12345678-5',
        ];

        $nombres = [
            ['Juan', 'Pérez'],
            ['María', 'González'],
            ['Pedro', 'Soto'],
            ['Ana', 'Rodríguez'],
            ['Luis', 'Martínez'],
            ['Carla', 'Hernández'],
            ['Diego', 'Castro'],
            ['Sofía', 'Vargas'],
            ['Rodrigo', 'Muñoz'],
            ['Camila', 'Rojas'],
        ];

        // Pool de especialidades y exámenes (AJUSTADO)
        $examenesPorEspecialidad = [
            'Radiología' => [
                ['codigo' => 'RX-TX',    'nombre' => 'Radiografía de Tórax',      'tipo' => 'IMAGEN'],
                ['codigo' => 'ECO-ABD',  'nombre' => 'Ecografía Abdominal',       'tipo' => 'IMAGEN'],
                ['codigo' => 'TAC-CR',   'nombre' => 'TAC de Cráneo',             'tipo' => 'IMAGEN'],
            ],
            'Laboratorio' => [
                ['codigo' => 'GLU',      'nombre' => 'Glucosa en sangre',         'tipo' => 'LAB'],
                ['codigo' => 'HB',       'nombre' => 'Hemograma completo',        'tipo' => 'LAB'],
                ['codigo' => 'TSH',      'nombre' => 'TSH',                       'tipo' => 'LAB'],
                ['codigo' => 'HBA1C',    'nombre' => 'Hemoglobina Glicosilada',   'tipo' => 'LAB'],
            ],
            'Cardiología' => [
                ['codigo' => 'ECG',      'nombre' => 'Electrocardiograma',        'tipo' => 'PROCEDIMIENTO'],
                ['codigo' => 'ECO-CARD', 'nombre' => 'Ecocardiograma',            'tipo' => 'IMAGEN'],
                ['codigo' => 'HOLTER',   'nombre' => 'Holter 24h',                'tipo' => 'PROCEDIMIENTO'],
            ],
            'Medicina General' => [
                ['codigo' => 'CTRL-GEN', 'nombre' => 'Control General',           'tipo' => 'CONSULTA'],
            ],
            'Endocrinología' => [
                ['codigo' => 'HBA1C',    'nombre' => 'Hemoglobina Glicosilada',   'tipo' => 'LAB'],
                ['codigo' => 'GLU',      'nombre' => 'Glucosa en sangre',         'tipo' => 'LAB'],
            ],
        ];

        // 🔑 Datos persistentes por paciente (SEXO FIJO + grupo sanguíneo + profesional)
        $pacienteData = [];
        foreach ($rutList as $i => $rut) {
            $grupoSanguineo = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'][rand(0, 7)];
            $idProfesional = rand(1000, 2000);
            $sexo = rand(0, 1) ? 'M' : 'F'; // ✅ Sexo fijo por paciente
            $pacienteData[$rut] = [
                'grupo_sanguineo' => $grupoSanguineo,
                'id_profesional'  => $idProfesional,
                'sexo'            => $sexo,
            ];
        }

        // 🔢 Contador único para número de caso
        $numeroCaso = 1000;

        foreach ($rutList as $i => $rut) {
            $nombreCompleto = $nombres[$i][0] . ' ' . $nombres[$i][1];
            $paciente = $pacienteData[$rut];

            // A) Radiología: exámenes distintos → especialidad = Radiología
            $this->insertGestion($rut, $nombreCompleto, $paciente, 'Radiología', $examenesPorEspecialidad['Radiología'][0], $numeroCaso++);
            $this->insertGestion($rut, $nombreCompleto, $paciente, 'Radiología', $examenesPorEspecialidad['Radiología'][1], $numeroCaso++);

            // B) Laboratorio: controles de glucosa → especialidad = Laboratorio
            $gluExamen = $examenesPorEspecialidad['Laboratorio'][0];
            $this->insertGestion($rut, $nombreCompleto, $paciente, 'Laboratorio', $gluExamen, $numeroCaso++, when: now()->subDays(20));
            $this->insertGestion($rut, $nombreCompleto, $paciente, 'Laboratorio', $gluExamen, $numeroCaso++, when: now()->subDays(7));
            $this->insertGestion($rut, $nombreCompleto, $paciente, 'Laboratorio', $gluExamen, $numeroCaso++, when: now());

            // C) Especialidad extra coherente
            if ($i % 2 === 0) {
                // Cardiología → examen de cardiología
                $this->insertGestion($rut, $nombreCompleto, $paciente, 'Cardiología', $examenesPorEspecialidad['Cardiología'][0], $numeroCaso++);
            } else {
                // Endocrinología → examen de endocrinología
                $this->insertGestion($rut, $nombreCompleto, $paciente, 'Endocrinología', $examenesPorEspecialidad['Endocrinología'][0], $numeroCaso++);
            }
        }
    }

    private function insertGestion(
        string $rut,
        string $nombre,
        array $paciente,
        string $especialidad, // ✅ Especialidad explícita
        array $examen,
        int $numeroCaso,
        ?Carbon $when = null
    ): void {
        $when = $when ?: Carbon::now()->subDays(rand(1, 30));

        // Determinar tipo_gestion según especialidad
        $tipoGestion = in_array($especialidad, ['Medicina General']) ? 'CONTROL' : 'EXAMEN';

        // Estado de solicitud aleatorio, pero coherente
        $estadoSolicitud = ['PENDIENTE', 'AGENDADA', 'ATENDIDA'][rand(0, 2)];
        $nivelUrgencia = ['BAJO', 'MEDIO', 'ALTO'][rand(0, 2)];

        // ✅ Si está ATENDIDA, debe tener fecha de atención
        $fechaAtencion = null;
        if ($estadoSolicitud === 'ATENDIDA') {
            $fechaAtencion = $when->copy()->addDays(rand(0, 2));
        }

        $base = [
            // Paciente
            'tipo_documento'       => 'RUT',
            'numero_documento'     => $rut,
            'nombre_paciente'      => $nombre,
            'fecha_nacimiento'     => Carbon::now()->subYears(rand(20, 70))->subDays(rand(0, 365))->format('Y-m-d'),
            'sexo'                 => $paciente['sexo'], // ✅ Sexo fijo
            'genero'               => null,
            'telefono'             => '+56 9 ' . rand(4000, 9999) . ' ' . rand(1000, 9999),
            'email'                => Str::slug($nombre, '.') . '@mail.com',
            'direccion'            => 'Calle ' . rand(1, 200) . ', Santiago',
            'grupo_sanguineo'      => $paciente['grupo_sanguineo'],
            'alergias_conocidas'   => null,

            // Preferencias
            'notificaciones_email' => true,
            'notificaciones_sms'   => true,
            'notificaciones_app'   => true,
            'idioma_preferido'     => 'es',

            // Solicitud
            'origen_solicitud'     => ['WEB', 'CALLCENTER', 'APP'][rand(0, 2)],
            'tipo_gestion'         => $tipoGestion,
            'especialidad'         => $especialidad, // ✅ Especialidad coherente
            'tipo_examen'          => $examen['tipo'],
            'examen_codigo'        => $examen['codigo'],
            'examen_nombre'        => $examen['nombre'],
            'fecha_solicitud'      => $when->copy()->subDays(rand(1, 5)),
            'fecha_cita_programada' => (clone $when)->addDays(rand(0, 3)),
            'lugar_cita'           => ['Clínica Centro', 'Sucursal Norte', 'Sucursal Sur'][rand(0, 2)],
            'estado_solicitud'     => $estadoSolicitud,
            'motivo_rechazo'       => null,
            'usuario_responsable'  => 'system',
            'numero_caso'          => str_pad($numeroCaso, 4, '0', STR_PAD_LEFT),

            // Atención
            'id_profesional'       => $paciente['id_profesional'],
            'tipo_atencion'        => ['CONTROL', 'PRIMERA', 'URGENCIA'][rand(0, 2)],
            'modalidad_atencion'   => ['PRESENCIAL', 'REMOTA'][rand(0, 1)],
            'fecha_atencion'       => $fechaAtencion, // ✅ Coherente con estado
            'duracion_minutos'     => $fechaAtencion ? rand(10, 45) : null, // Solo si hay atención
            'estado_asistencia'    => $fechaAtencion ? 'ASISTIDA' : ['NO_REALIZADA', 'INASISTENTE'][rand(0, 1)],
            'motivo_no_asistencia' => null,

            // Clínico
            'motivo_consulta'      => null,
            'anamnesis'            => null,
            'antecedentes_personales' => null,
            'antecedentes_familiares' => null,
            'factores_riesgo'      => null,
            'examen_fisico'        => null,
            'diagnostico_principal' => null,
            'diagnosticos_secundarios' => null,
            'impresion_diagnostica' => null,
            'plan_de_accion'       => null,
            'resumen_atencion'     => null,
            'seguimiento_requerido' => (bool)rand(0, 1),
            'fecha_proximo_control' => rand(0, 1) ? Carbon::now()->addDays(rand(15, 60))->format('Y-m-d') : null,

            // Signos vitales (solo si hay atención)
            'peso_kg'              => $fechaAtencion ? rand(55, 95) : null,
            'talla_cm'             => $fechaAtencion ? rand(150, 190) : null,
            'imc'                  => $fechaAtencion ? rand(19, 33) : null,
            'presion_arterial'     => $fechaAtencion ? rand(100, 140) . '/' . rand(60, 95) : null,
            'frecuencia_cardiaca'  => $fechaAtencion ? rand(60, 95) : null,
            'saturacion_oxigeno'   => $fechaAtencion ? rand(92, 99) : null,
            'temperatura'          => $fechaAtencion ? number_format(rand(360, 378) / 10, 1) : null,
            'circunferencia_abdominal' => $fechaAtencion ? rand(70, 110) : null,

            // Medicamentos
            'medicamentos_activos' => null,
            'alergias_medicamentosas' => null,
            'interacciones_conocidas' => null,

            // Crónicas
            'tiene_diabetes'       => (bool)rand(0, 1),
            'tipo_diabetes'        => null,
            'ultima_hba1c'         => null,
            'fecha_ultima_hba1c'   => null,
            'tiene_hta'            => (bool)rand(0, 1),
            'tiene_asma'           => (bool)rand(0, 1),
            'tiene_erc'            => (bool)rand(0, 1),

            // Recordatorios
            'recordatorios_activos' => null,
            'controles_programados' => null,
            'fecha_proximo_control_glucosa' => null,
            'fecha_proxima_vacuna' => null,
            'frecuencia_control_hta' => null,

            // Receta/Licencia/Informe
            'tiene_receta'         => (bool)rand(0, 1),
            'detalle_receta'       => null,
            'tiene_licencia'       => (bool)rand(0, 1),
            'tipo_licencia'        => null,
            'dias_licencia'        => null,
            'fecha_inicio_licencia' => null,
            'fecha_fin_licencia'   => null,
            'justificacion_licencia' => null,
            'tiene_informe'        => (bool)rand(0, 1),
            'contenido_informe'    => null,

            // PDFs
            'url_pdf_receta'       => 'receta_prueba.pdf',
            'url_pdf_licencia'     => 'licencia_prueba.pdf',
            'url_pdf_informe'      => 'informe_prueba.pdf',

            // Seguridad
            'consentimiento_informado' => (bool)rand(0, 1),
            'nivel_urgencia'       => $nivelUrgencia,
            'cobertura_validada'   => (bool)rand(0, 1),

            // Auditoría
            'firma_profesional_hash' => null,
            'ip_registro'          => request()?->ip() ?? '127.0.0.1',
            'usuario_registro'     => 'seeder',
            'created_at'           => $when->copy(),
            'updated_at'           => $when->copy(),
        ];

        GestionSaludCompleta::create($base);
    }
}
