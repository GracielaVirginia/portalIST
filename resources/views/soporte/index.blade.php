@extends('layouts.app')

@section('title', 'Enviar un ticket')

@section('content')
<div class="max-w-2xl mx-auto py-10">
  <h1 class="text-3xl font-bold text-center text-sky-800 dark:text-sky-200 mb-8">
    Enviar un ticket
  </h1>

  @if (session('ok'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
      {{ session('ok') }}
    </div>
  @endif

  <form method="POST" action="{{ route('soporte.store') }}" enctype="multipart/form-data" class="space-y-6 bg-white dark:bg-gray-900 p-6 rounded-2xl shadow">
    @csrf

    <div>
      <label class="block text-sm font-semibold mb-1 text-gray-700 dark:text-gray-200">Correo electrónico *</label>
      <input type="email" name="email" value="{{ old('email') }}" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100" required>
    </div>

    <div>
      <label class="block text-sm font-semibold mb-1 text-gray-700 dark:text-gray-200">RUT *</label>
      <input type="text" name="rut" value="{{ old('rut') }}" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100" required>
    </div>

    <div>
      <label class="block text-sm font-semibold mb-1 text-gray-700 dark:text-gray-200">Número de teléfono</label>
      <input type="text" name="telefono" value="{{ old('telefono') }}" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
    </div>

    <div>
      <label class="block text-sm font-semibold mb-1 text-gray-700 dark:text-gray-200">Por favor detalla tu solicitud *</label>
      <textarea name="detalle" rows="5" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100" required>{{ old('detalle') }}</textarea>
    </div>

    <div>
      <label class="block text-sm font-semibold mb-1 text-gray-700 dark:text-gray-200">Datos adjuntos</label>
      <input type="file" name="archivo" class="w-full text-sm text-gray-600 dark:text-gray-300">
    </div>

    <button type="submit" class="w-full bg-sky-700 hover:bg-sky-800 text-white font-semibold py-2 px-4 rounded-lg transition">
      Enviar Ticket
    </button>
  </form>
</div>
@endsection
