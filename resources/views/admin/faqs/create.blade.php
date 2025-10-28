{{-- create --}}
@extends('layouts.app')
@section('title','Crear FAQ')
@section('content')
<div class="px-6 py-6 max-w-3xl mx-auto bg-white dark:bg-gray-900 rounded-2xl shadow p-4">
  <h1 class="text-xl font-bold mb-4">Nueva FAQ</h1>
  {{-- Header: título + tooltip de ayuda --}}
<div class="mb-6 flex items-start justify-between">
  <div class="flex items-center gap-3">
    <h2 class="text-2xl font-bold text-purple-900 dark:text-purple-100">
      Preguntas frecuentes
    </h2>

    {{-- Tooltip "i" --}}
    <div class="relative group">
      <span class="inline-flex h-6 w-6 items-center justify-center rounded-full
                   bg-purple-100 text-purple-900 border border-purple-200
                   text-xs font-bold select-none cursor-default">
        i
      </span>

      {{-- Panel tooltip (hover) --}}
      <div class="absolute left-0 mt-2 w-80 rounded-xl border border-gray-200 dark:border-gray-700
                  bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-200 text-sm p-3 shadow-xl
                  opacity-0 scale-95 translate-y-1 group-hover:opacity-100 group-hover:scale-100 group-hover:translate-y-0
                  transition pointer-events-none group-hover:pointer-events-auto z-10">
        <p class="mb-2">
          Aquí configuras las <strong>FAQs</strong> que verá el paciente en el chat de ayuda.
        </p>
        <ul class="list-disc pl-5 space-y-1">
          <li><strong>Pregunta:</strong> el título visible.</li>
          <li><strong>Respuesta:</strong> texto libre (soporta párrafos).</li>
          <li><strong>Orden:</strong> número para ordenar (menor = primero).</li>
          <li><strong>Visible:</strong> activa o desactiva sin borrar.</li>
        </ul>
        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
          El buscador del chat filtra por pregunta y respuesta en tiempo real.
        </p>
      </div>
    </div>
  </div>
</div>

  <form method="POST" action="{{ route('admin.faqs.store') }}">
    @include('admin.faqs._form')
  </form>
</div>
@endsection
