{{-- resources/views/ver-resultado.blade.php --}}
@extends('layouts.app', ['topbar' => true, 'navbar' => true])

@section('title', 'Detalle del resultado')

@section('content')
@php
  /** @var \App\Models\GestionSaludCompleta $g */
  $g = $gestion ?? $gestion ?? null;  // por si lo pasas como 'gestion' o 'resultado'
  $labelEsp = function($raw) {
      $u = \Illuminate\Support\Str::upper(\Illuminate\Support\Str::ascii((string)$raw));
      return \Illuminate\Support\Str::contains($u,'RADIO')||\Illuminate\Support\Str::contains($u,'RX') ? 'Radiografía' :
             (\Illuminate\Support\Str::contains($u,'ECOG')||\Illuminate\Support\Str::contains($u,'ECO') ? 'Ecografía' :
             (\Illuminate\Support\Str::contains($u,'LAB') ? 'Laboratorio' :
             (\Illuminate\Support\Str::contains($u,'ENDO') ? 'Endocrinología' :
             (\Illuminate\Support\Str::contains($u,'INTERNA')||\Illuminate\Support\Str::contains($u,'MED') ? 'Medicina Interna' : ($raw ?: 'Otro')))));
  };
@endphp

<div class="container mx-auto px-4 sm:px-6 lg:px-8 pt-4 pb-10">
  <div class="flex items-center justify-between">
    <h1 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
      {{ $g?->examen_nombre ?: $labelEsp($g?->especialidad) }}
    </h1>
    <a href="{{ url()->previous() }}"
       class="inline-flex items-center rounded-xl border border-gray-200 dark:border-gray-700 px-3 py-1.5 text-sm
              text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-900">Volver</a>
  </div>

  <div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-4">
    {{-- Card principal --}}
    <div class="lg:col-span-2 rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-5 shadow-sm">
      <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
        <div>
          <dt class="text-gray-500 dark:text-gray-400">Especialidad</dt>
          <dd class="font-medium text-gray-900 dark:text-gray-100">{{ $labelEsp($g?->especialidad) }}</dd>
        </div>
        <div>
          <dt class="text-gray-500 dark:text-gray-400">Fecha</dt>
          <dd class="font-medium text-gray-900 dark:text-gray-100">
            {{ optional($g?->fecha_atencion)->format('Y-m-d H:i') ?: optional($g?->created_at)->format('Y-m-d H:i') }}
          </dd>
        </div>
        <div>
          <dt class="text-gray-500 dark:text-gray-400">Código examen</dt>
          <dd class="font-medium text-gray-900 dark:text-gray-100">{{ $g?->examen_codigo ?: '—' }}</dd>
        </div>
        <div>
          <dt class="text-gray-500 dark:text-gray-400">Estado</dt>
          <dd class="font-medium text-gray-900 dark:text-gray-100">
            {{ $g?->tiene_informe ? 'DISPONIBLE' : ($g?->estado_solicitud ?: '—') }}
          </dd>
        </div>
        <div class="sm:col-span-2">
          <dt class="text-gray-500 dark:text-gray-400">Resumen / Observaciones</dt>
          <dd class="mt-1 whitespace-pre-wrap text-gray-800 dark:text-gray-200">
            {{ $g?->resumen_atencion ?: $g?->impresion_diagnostica ?: '—' }}
          </dd>
        </div>
      </dl>

      @if($g?->url_pdf_informe)
        <div class="mt-5">
          <a href="{{ $g->url_pdf_informe }}" target="_blank" rel="noopener"
             class="inline-flex items-center rounded-xl bg-purple-900 text-white px-4 py-2 text-sm font-semibold hover:opacity-90">
            Ver informe en PDF
          </a>
        </div>
      @endif
    </div>

    {{-- Meta / paciente --}}
    <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-5 shadow-sm">
      <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-3">Datos del paciente</h3>
      <dl class="space-y-2 text-sm">
        <div><dt class="text-gray-500 dark:text-gray-400">Nombre</dt><dd class="font-medium text-gray-900 dark:text-gray-100">{{ $g?->nombre_paciente ?: '—' }}</dd></div>
        <div><dt class="text-gray-500 dark:text-gray-400">Documento</dt><dd class="font-medium text-gray-900 dark:text-gray-100">{{ $g?->tipo_documento }} {{ $g?->numero_documento }}</dd></div>
        <div><dt class="text-gray-500 dark:text-gray-400">Profesional</dt><dd class="font-medium text-gray-900 dark:text-gray-100">{{ $g?->id_profesional ?: '—' }}</dd></div>
        <div><dt class="text-gray-500 dark:text-gray-400">Lugar</dt><dd class="font-medium text-gray-900 dark:text-gray-100">{{ $g?->lugar_cita ?: '—' }}</dd></div>
      </dl>
    </div>
  </div>
</div>
@endsection
