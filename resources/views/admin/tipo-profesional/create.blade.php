@extends('layouts.admin')
@section('title', 'Nuevo tipo de profesional')

@section('admin')
<div class="px-6 py-6 max-w-3xl">
  <h1 class="text-2xl font-bold text-purple-700 dark:text-purple-200 mb-4">
    ➕ Nuevo tipo de profesional
  </h1>

  <form action="{{ route('tipos.store') }}" method="POST"
        class="space-y-4 bg-white dark:bg-gray-900 p-5 rounded-xl border dark:border-gray-700">
    @include('admin.tipo-profesional._form')
    <div class="text-right">
      <a href="{{ route('tipos.index') }}" class="px-4 py-2 rounded border mr-2">Cancelar</a>
      <button class="px-4 py-2 rounded bg-purple-600 hover:bg-purple-700 text-white">Guardar</button>
    </div>
  </form>
</div>
@endsection
