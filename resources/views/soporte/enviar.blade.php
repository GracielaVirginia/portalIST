@extends('layouts.app')

@section('title', 'Enviar un ticket de ayuda')

@section('content')
    <div class="min-h-screen bg-purple-50 dark:bg-gray-950 py-10 flex items-center justify-center">
        <div
            class="w-full max-w-2xl bg-white dark:bg-gray-900 rounded-2xl shadow-xl p-8 border border-purple-100 dark:border-gray-700">

            {{-- Título --}}
            <h1 class="text-3xl font-bold text-center text-purple-800 dark:text-purple-300 mb-8">
                Enviar un ticket de ayuda
            </h1>


            {{-- Formulario --}}
            <form method="POST" action="{{ route('soporte.store') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf

                {{-- Correo --}}
                <div>
                    <label class="block text-sm font-semibold mb-1 text-gray-800 dark:text-gray-200">
                        Correo electrónico *
                    </label>
                    <input type="email" name="email" value="{{ old('email') }}"
                        class="w-full rounded-lg
       border-2 border-purple-200
       bg-white dark:bg-gray-800
       text-gray-900 dark:text-gray-100 p-3
       focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                        required>
                </div>

                {{-- RUT --}}
                <div>
                    <label class="block text-sm font-semibold mb-1 text-gray-800 dark:text-gray-200">
                        RUT *
                    </label>
                    <input type="text" name="rut" value="{{ old('rut') }}"
                        class="w-full rounded-lg
       border-2 border-purple-200
       bg-white dark:bg-gray-800
       text-gray-900 dark:text-gray-100 p-3
       focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                        required>
                </div>

                {{-- Teléfono --}}
                <div>
                    <label class="block text-sm font-semibold mb-1 text-gray-800 dark:text-gray-200">
                        Número de teléfono
                    </label>
                    <input type="text" name="telefono" value="{{ old('telefono') }}"
                        class="w-full rounded-lg
       border-2 border-purple-200
       bg-white dark:bg-gray-800
       text-gray-900 dark:text-gray-100 p-3
       focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                </div>

                {{-- Detalle --}}
                <div>
                    <label class="block text-sm font-semibold mb-1 text-gray-800 dark:text-gray-200">
                        Por favor detalla tu solicitud *
                    </label>
                    <textarea name="detalle" rows="5"
                        class="w-full rounded-lg
       border-2 border-purple-200
       bg-white dark:bg-gray-800
       text-gray-900 dark:text-gray-100 p-3
       focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                        required>{{ old('detalle') }}</textarea>
                </div>

                {{-- Archivo --}}
                <div>
                    <label class="block text-sm font-semibold mb-1 text-gray-800 dark:text-gray-200">
                        Datos adjuntos
                    </label>
                    <input type="file" name="archivo" accept=".pdf,.jpg,.jpeg,.png"
                        class="block w-full text-sm
              border-2 border-purple-200 rounded-lg
              bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300
              file:mr-4 file:py-2 file:px-3
              file:rounded-md file:border-0
              file:bg-purple-700 file:text-white
              hover:file:bg-purple-800
              focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500">

                    {{-- Botones --}}
                    <div class="flex items-center justify-between pt-4">
                        <button type="button" onclick="window.history.back()"
                            class="inline-flex items-center gap-2 bg-gray-200 dark:bg-gray-800 text-gray-700 dark:text-gray-200
                       px-4 py-2 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-700 transition">
                            ⬅ Volver
                        </button>

                        <button type="submit"
                            class="inline-flex items-center gap-2 bg-purple-900 hover:bg-purple-800
                       text-white font-semibold px-5 py-2.5 rounded-lg shadow transition">
                            ✉️ Enviar Ticket
                        </button>
                    </div>
            </form>
        </div>
    </div>
@endsection
