@extends('layouts.admin')
@section('title', 'Editar administrador — Admin')

@section('admin')
<div class="px-6 py-6">
  {{-- Botones superiores --}}
  <div class="flex items-center justify-between mb-4">
    <div class="flex items-center gap-3">
  <x-admin.nav-actions
    backHref="{{ route('admin.dashboard') }}"
    logoutRoute="admin.logout"
    variant="inline"   {{-- o "sticky" si la tabla es larga --}}
  />      </div>
  </div>

  {{-- Título + ayuda --}}
  <div class="mb-6 flex items-start justify-between">
    <div class="flex items-center gap-3">
      <h2 class="text-2xl font-bold text-purple-900 dark:text-purple-100">
        Editar administrador
      </h2>
      <div class="relative group">
        <span class="inline-flex h-6 w-6 items-center justify-center rounded-full
                     bg-purple-100 text-purple-900 border border-purple-200
                     text-xs font-bold select-none cursor-default">i</span>
        <div class="absolute left-0 mt-2 w-80 rounded-xl border border-gray-200 dark:border-gray-700
                    bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-200 text-sm p-3 shadow-xl
                    opacity-0 scale-95 translate-y-1 group-hover:opacity-100 group-hover:scale-100 group-hover:translate-y-0
                    transition pointer-events-none group-hover:pointer-events-auto z-10">
          <p class="mb-2">Actualiza los datos del administrador. La contraseña es opcional al editar.</p>
          <ul class="list-disc pl-5 space-y-1">
            <li>Si no cambias la contraseña, deja los campos vacíos.</li>
            <li>El <strong>Usuario</strong>, <strong>Email</strong> y <strong>RUT</strong> deben seguir siendo únicos.</li>
          </ul>
        </div>
      </div>
    </div>
  </div>

  <form action="{{ route('admin.administradores.update', $admin) }}" method="POST" class="space-y-4">
    @method('PUT')
    @include('admin.administradores._form', ['admin' => $admin, 'mode' => 'edit'])
  </form>
</div>
@endsection
