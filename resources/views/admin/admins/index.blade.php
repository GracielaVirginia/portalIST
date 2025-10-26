@extends('layouts.app')

@section('content')
<div class="flex">
    <!-- Sidebar -->
    <div class="w-64 bg-gray-800 text-white h-screen p-4">
        <h2 class="text-xl font-bold mb-6">Menú Administrador</h2>
        <ul class="space-y-2">
            <li>
                <a href="#" class="block px-4 py-2 rounded hover:bg-gray-700">Dashboard</a>
            </li>
            <li>
                <a href="#" class="block px-4 py-2 rounded hover:bg-gray-700">Usuarios</a>
            </li>
            <li>
                <a href="#" class="block px-4 py-2 rounded hover:bg-gray-700">Configuración</a>
            </li>
        </ul>
    </div>

    <!-- Contenido Principal -->
    <div class="flex-1 p-6">
        <h1 class="text-2xl font-bold mb-6">Panel de Administración</h1>

        <!-- Dropdowns con etiquetas estilo purple-900 -->
        <div class="flex flex-wrap gap-4 mb-6">
            <!-- Dropdown Especialidad -->
            <div class="relative inline-block text-left">
                <div>
                    <button type="button" class="inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-purple-900 hover:bg-purple-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500" id="especialidad-menu-button" aria-expanded="true" aria-haspopup="true">
                        Especialidad
                        <svg class="-mr-1 ml-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
                <div class="origin-top-right absolute left-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 hidden" role="menu" aria-orientation="vertical" aria-labelledby="especialidad-menu-button" id="especialidad-menu">
                    <div class="py-1" role="none">
                        <a href="" class="text-gray-700 block px-4 py-2 text-sm hover:bg-gray-100">Crear</a>
                        <a href="#" class="text-gray-700 block px-4 py-2 text-sm hover:bg-gray-100">Modificar</a>
                        <a href="" class="text-gray-700 block px-4 py-2 text-sm hover:bg-gray-100">Ver Todas</a>
                        <a href="#" class="text-gray-700 block px-4 py-2 text-sm hover:bg-gray-100">Eliminar</a>
                    </div>
                </div>
            </div>

            <!-- Dropdown Nombre Examen -->
            <div class="relative inline-block text-left">
                <div>
                    <button type="button" class="inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-purple-900 hover:bg-purple-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500" id="examen-menu-button" aria-expanded="true" aria-haspopup="true">
                        Nombre Examen
                        <svg class="-mr-1 ml-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
                <div class="origin-top-right absolute left-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 hidden" role="menu" aria-orientation="vertical" aria-labelledby="examen-menu-button" id="examen-menu">
                    <div class="py-1" role="none">
                        <a href="{{ route('admin.examen_nombre.create') }}" class="text-gray-700 block px-4 py-2 text-sm hover:bg-gray-100">Crear</a>
                        <a href="#" class="text-gray-700 block px-4 py-2 text-sm hover:bg-gray-100">Modificar</a>
                        <a href="{{ route('admin.examen_nombre.index') }}" class="text-gray-700 block px-4 py-2 text-sm hover:bg-gray-100">Ver Todas</a>
                        <a href="#" class="text-gray-700 block px-4 py-2 text-sm hover:bg-gray-100">Eliminar</a>
                    </div>
                </div>
            </div>

            <!-- Dropdown Tipo Atención -->
            <div class="relative inline-block text-left">
                <div>
                    <button type="button" class="inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-purple-900 hover:bg-purple-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500" id="tipo-atencion-menu-button" aria-expanded="true" aria-haspopup="true">
                        Tipo Atención
                        <svg class="-mr-1 ml-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
                <div class="origin-top-right absolute left-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 hidden" role="menu" aria-orientation="vertical" aria-labelledby="tipo-atencion-menu-button" id="tipo-atencion-menu">
                    <div class="py-1" role="none">
                        <a href="#" class="text-gray-700 block px-4 py-2 text-sm hover:bg-gray-100">Crear</a>
                        <a href="#" class="text-gray-700 block px-4 py-2 text-sm hover:bg-gray-100">Modificar</a>
                        <a href="#" class="text-gray-700 block px-4 py-2 text-sm hover:bg-gray-100">Ver Todas</a>
                        <a href="#" class="text-gray-700 block px-4 py-2 text-sm hover:bg-gray-100">Eliminar</a>
                    </div>
                </div>
            </div>

            <!-- Dropdown Lugar Cita -->
            <div class="relative inline-block text-left">
                <div>
                    <button type="button" class="inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-purple-900 hover:bg-purple-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500" id="lugar-cita-menu-button" aria-expanded="true" aria-haspopup="true">
                        Lugar Cita
                        <svg class="-mr-1 ml-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
                <div class="origin-top-right absolute left-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 hidden" role="menu" aria-orientation="vertical" aria-labelledby="lugar-cita-menu-button" id="lugar-cita-menu">
                    <div class="py-1" role="none">
                        <a href="#" class="text-gray-700 block px-4 py-2 text-sm hover:bg-gray-100">Crear</a>
                        <a href="#" class="text-gray-700 block px-4 py-2 text-sm hover:bg-gray-100">Modificar</a>
                        <a href="#" class="text-gray-700 block px-4 py-2 text-sm hover:bg-gray-100">Ver Todas</a>
                        <a href="#" class="text-gray-700 block px-4 py-2 text-sm hover:bg-gray-100">Eliminar</a>
                    </div>
                </div>
            </div>

            <!-- Dropdown Tipo Gestión -->
            <div class="relative inline-block text-left">
                <div>
                    <button type="button" class="inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-purple-900 hover:bg-purple-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500" id="tipo-gestion-menu-button" aria-expanded="true" aria-haspopup="true">
                        Tipo Gestión
                        <svg class="-mr-1 ml-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
                <div class="origin-top-right absolute left-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 hidden" role="menu" aria-orientation="vertical" aria-labelledby="tipo-gestion-menu-button" id="tipo-gestion-menu">
                    <div class="py-1" role="none">
                        <a href="" class="text-gray-700 block px-4 py-2 text-sm hover:bg-gray-100">Crear</a>
                        <a href="#" class="text-gray-700 block px-4 py-2 text-sm hover:bg-gray-100">Modificar</a>
                        <a href="" class="text-gray-700 block px-4 py-2 text-sm hover:bg-gray-100">Ver Todas</a>
                        <a href="#" class="text-gray-700 block px-4 py-2 text-sm hover:bg-gray-100">Eliminar</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contenido dinámico -->
        <div id="dynamic-content">
            @yield('dynamic-content')
        </div>
    </div>
</div>

<!-- Script para manejar los dropdowns -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dropdownButtons = document.querySelectorAll('[id$="-menu-button"]');
        dropdownButtons.forEach(button => {
            button.addEventListener('click', function() {
                const menuId = this.id.replace('-menu-button', '-menu');
                const menu = document.getElementById(menuId);
                menu.classList.toggle('hidden');
            });
        });
    });
</script>
@endsection
