@extends('layouts.admin')
@section('title', 'Tipos de profesionales')

@section('admin')
<div class="px-6 py-6">
  <div class="flex items-center justify-between mb-4">
      <x-admin.nav-actions
    backHref="{{ route('admin.dashboard') }}"
    logoutRoute="admin.logout"
    variant="inline"
  />
    <h1 class="text-2xl font-bold text-purple-700 dark:text-purple-200">🏷️ Tipos de profesionales</h1>
    <a href="{{ route('tipos-profesionales.create') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded">
      Nuevo tipo
    </a>
  </div>

  <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4 shadow-sm">
    <table class="min-w-full">
      <thead>
        <tr class="text-left text-xs sm:text-sm text-purple-900 dark:text-purple-200">
          <th class="py-2">ID</th>
          <th class="py-2">Sucursal</th>
          <th class="py-2">Nombre</th>
          <th class="py-2">Descripción</th>
          <th class="py-2 text-center">Acciones</th>
        </tr>
      </thead>
      <tbody class="text-sm">
        @foreach($tipos as $t)
          <tr class="border-b border-gray-100 dark:border-gray-800">
            <td class="py-2">{{ $t->id }}</td>
            <td class="py-2">{{ $t->sucursal->nombre ?? '-' }}</td>
            <td class="py-2">{{ $t->nombre }}</td>
            <td class="py-2">{{ $t->descripcion }}</td>
            <td class="py-2 text-center">
              <div class="flex justify-center gap-2">
                <a href="{{ route('tipos-profesionales.edit', $t) }}"
                   class="border border-purple-400 text-purple-700 rounded-full px-3 py-1 text-xs font-semibold hover:bg-purple-50">
                  Editar
                </a>
                <form action="{{ route('tipos.destroy', $t) }}" method="POST"
                      onsubmit="return confirm('¿Eliminar tipo?')" class="inline">
                  @csrf @method('DELETE')
                  <button class="border border-red-400 text-red-700 rounded-full px-3 py-1 text-xs font-semibold hover:bg-red-50">
                    Eliminar
                  </button>
                </form>
              </div>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection
