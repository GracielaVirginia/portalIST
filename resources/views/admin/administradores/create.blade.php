@extends('layouts.app')
@section('title', 'Crear administrador — Admin')

@section('content')
<div class="px-6 py-6">
  {{-- Botones superiores --}}
  <div class="flex items-center justify-between mb-4">
    <div class="flex items-center gap-3">
      <a href="{{ route('admin.administradores.index') }}"
         class="inline-flex items-center gap-2 bg-purple-900 text-white text-sm font-semibold px-4 py-2 rounded-full shadow-sm hover:bg-purple-800 hover:shadow-md transition">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
        </svg>
        Volver al listado
      </a>

      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit"
                class="inline-flex items-center gap-2 bg-red-600 text-white text-sm font-semibold px-4 py-2 rounded-full shadow-sm hover:bg-red-500 hover:shadow-md transition">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1" />
          </svg>
          Cerrar sesión
        </button>
      </form>
    </div>
  </div>

  {{-- Título + ayuda --}}
  <div class="mb-6 flex items-start justify-between">
    <div class="flex items-center gap-3">
      <h2 class="text-2xl font-bold text-purple-900 dark:text-purple-100">
        Crear administrador
      </h2>
      <div class="relative group">
        <span class="inline-flex h-6 w-6 items-center justify-center rounded-full
                     bg-purple-100 text-purple-900 border border-purple-200
                     text-xs font-bold select-none cursor-default">i</span>
        <div class="absolute left-0 mt-2 w-80 rounded-xl border border-gray-200 dark:border-gray-700
                    bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-200 text-sm p-3 shadow-xl
                    opacity-0 scale-95 translate-y-1 group-hover:opacity-100 group-hover:scale-100 group-hover:translate-y-0
                    transition pointer-events-none group-hover:pointer-events-auto z-10">
          <p class="mb-2">Completa los datos del administrador. La contraseña es obligatoria al crear.</p>
          <ul class="list-disc pl-5 space-y-1">
            <li><strong>Usuario</strong>, <strong>Email</strong> y <strong>RUT</strong> deben ser únicos.</li>
            <li>Marca <strong>Activo</strong> para habilitar su acceso.</li>
          </ul>
        </div>
      </div>
    </div>
  </div>

  <form action="{{ route('admin.administradores.store') }}" method="POST" class="space-y-4">
    @include('admin.administradores._form', ['admin' => null, 'mode' => 'create'])
  </form>
</div>
@endsection
