@extends('layouts.app')

@section('content')
<!-- 💜 Fondo morado a toda la página -->
<div class="min-h-screen bg-purple-100 flex items-center justify-center px-4 py-10">

  <!-- 📄 Card blanca centrada -->
  <div class="w-full max-w-2xl bg-white rounded-2xl shadow-md ring-1 ring-purple-200 p-8 text-center">
    <a href="{{ route('portal.historial.index') }}"
       class="inline-flex items-center gap-2 text-sm text-purple-700 hover:underline mb-4">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
        <path d="M15.75 19.5 8.25 12l7.5-7.5"/>
      </svg>
      Volver a Mi historial médico
    </a>

    <div class="flex flex-col items-center gap-4">
      <!-- Icono -->
      <span class="inline-flex items-center justify-center h-12 w-12 rounded-xl bg-purple-50 ring-1 ring-purple-200">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-purple-600" viewBox="0 0 24 24" fill="currentColor">
          <path d="M10 4H4a2 2 0 0 0-2 2v2h20V8a2 2 0 0 0-2-2h-8l-2-2Z" opacity=".2"/>
          <path d="M2 9.5A2.5 2.5 0 0 1 4.5 7h15A2.5 2.5 0 0 1 22 9.5v8A2.5 2.5 0 0 1 19.5 20h-15A2.5 2.5 0 0 1 2 17.5v-8Z"/>
        </svg>
      </span>

      <!-- Texto -->
      <div>
        <h1 class="text-3xl font-semibold text-purple-800">Agregar documento</h1>
        <p class="mt-1 text-sm text-gray-600">
          Sube y organiza tus documentos médicos para mejorar tu control de salud.
        </p>
      </div>

      <!-- Botón (tu componente) -->
      <div class="mt-3">
        <x-portal.upload-document />
      </div>
    </div>
  </div>

</div>
@endsection
