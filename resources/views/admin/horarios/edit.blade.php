@extends('layouts.admin')
@section('title','Editar horario')

@section('admin')
<div class="px-6 py-6 max-w-3xl">
  <h1 class="text-2xl font-bold text-purple-700 dark:text-purple-200 mb-4">✎ Editar horario</h1>

  <form action="{{ route('horarios.update', $horario) }}" method="POST"
        class="space-y-4 bg-white dark:bg-gray-900 p-5 rounded-xl border dark:border-gray-700">
    @method('PUT')
    @include('admin.horarios._form')
    <div class="text-right">
      <a href="{{ route('horarios.index') }}" class="px-4 py-2 rounded border mr-2">Cancelar</a>
      <button class="px-4 py-2 rounded bg-purple-600 hover:bg-purple-700 text-white">Guardar cambios</button>
    </div>
  </form>
</div>
@endsection
