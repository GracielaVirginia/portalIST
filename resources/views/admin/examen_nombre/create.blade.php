@extends('layouts.app')

@section('content')
    <!-- Incluir el sidebar y los dropdowns del admin -->
    @include('admin.admins')

    <!-- Contenido específico de la vista create -->
    <div class="container mx-auto px-4 py-6">
        <h1 class="text-2xl font-bold mb-6">Crear Nuevo Examen</h1>

        <form method="POST" action="{{ route('admin.examen_nombre.store') }}" class="bg-white p-6 rounded-lg shadow-md">
            @csrf
            <div class="mb-4">
                <label for="codigo" class="block text-sm font-medium text-gray-700">Código:</label>
                <input type="text" name="codigo" id="codigo" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
            </div>

            <div class="mb-4">
                <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre:</label>
                <input type="text" name="nombre" id="nombre" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
            </div>

            <div class="mb-4">
                <label for="tipo" class="block text-sm font-medium text-gray-700">Tipo:</label>
                <input type="text" name="tipo" id="tipo" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
            </div>

            <div class="mb-4">
                <label for="especialidad_id" class="block text-sm font-medium text-gray-700">Especialidad:</label>
                <select name="especialidad_id" id="especialidad_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                    @foreach($especialidades as $especialidad)
                        <option value="{{ $especialidad->id }}">{{ $especialidad->name }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Guardar
            </button>
        </form>
    </div>
@endsection
