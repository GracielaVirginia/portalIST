@extends('layouts.app')

@section('title', 'Validación — Elige un método')

@section('content')
<div class="min-h-[88vh] bg-purple-50 dark:bg-gray-900 flex items-center">
  <div class="w-full max-w-5xl mx-auto px-6 py-8">
    {{-- Header: logo + título --}}
    <div class="flex items-center justify-center mb-4">
      <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-20 h-20 object-contain">
    </div>
    <h1 class="text-2xl font-bold text-purple-800 dark:text-white text-center">Validación</h1>
    <p class="text-sm text-purple-700/80 dark:text-gray-300 text-center">Portal Pacientes</p>

    {{-- Acciones: Salir + Toggle tema --}}
    <div class="mt-4 flex items-center justify-between">
      <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
      <button
        type="button"
        onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold bg-gray-200 text-gray-700 hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7" />
        </svg>
        Salir
      </button>

      <button id="themeToggle"
        class="px-4 py-2 rounded-xl text-sm font-semibold bg-purple-900 text-white hover:bg-purple-800">
        🌙 Modo Oscuro
      </button>
    </div>

    {{-- Pasos --}}
    <div class="mt-6 flex items-center gap-3">
      <div class="flex-1 text-center">
        <div class="w-10 h-10 mx-auto rounded-full flex items-center justify-center bg-gray-300 dark:bg-gray-700 text-gray-700 dark:text-white font-bold">1</div>
        <p class="mt-1 text-xs font-semibold text-purple-800 dark:text-white">Registrarse</p>
      </div>
      <div class="h-1 flex-1 bg-purple-300/60 dark:bg-gray-700 rounded"></div>
      <div class="flex-1 text-center">
        <div class="w-10 h-10 mx-auto rounded-full flex items-center justify-center bg-purple-900 text-white font-bold">2</div>
        <p class="mt-1 text-xs font-semibold text-gray-500 dark:text-gray-400">Verificar Identidad</p>
      </div>
      <div class="h-1 flex-1 bg-purple-300/60 dark:bg-gray-700 rounded"></div>
      <div class="flex-1 text-center">
        <div class="w-10 h-10 mx-auto rounded-full flex items-center justify-center bg-gray-300 dark:bg-gray-700 text-gray-700 dark:text-white font-bold">3</div>
        <p class="mt-1 text-xs font-semibold text-gray-500 dark:text-gray-400">Ver Resultados</p>
      </div>
    </div>

    {{-- Contenido --}}
    <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
      {{-- Opciones --}}
      <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow">
        <h2 class="text-lg font-semibold text-purple-900 dark:text-white">Necesitamos validar tus datos</h2>
        <p class="text-sm text-purple-700/80 dark:text-gray-300 mt-1">¿Cómo deseas validar?</p>

        <div class="mt-4 space-y-3">
          <label class="flex items-center gap-2 text-sm text-gray-800 dark:text-gray-100">
            <input type="radio" name="validacion" value="telefono" class="accent-purple-700">
            Validar con número de teléfono
          </label>
          <label class="flex items-center gap-2 text-sm text-gray-800 dark:text-gray-100">
            <input type="radio" name="validacion" value="email" class="accent-purple-700">
            Validar con correo electrónico
          </label>
          <label class="flex items-center gap-2 text-sm text-gray-800 dark:text-gray-100">
            <input type="radio" name="validacion" value="examen" class="accent-purple-700">
            Validar con un examen realizado
          </label>
        </div>
      </div>

      {{-- Formulario --}}
      <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow">
        <form id="validacionForm" method="POST" action="{{ route('verificar-usuario') }}" class="space-y-5">
          @csrf
          <input type="hidden" id="tipo_validacion" name="tipo_validacion">
          <input type="hidden" id="valor" name="valor">

          {{-- Teléfono --}}
          <div id="telefonoSection" class="hidden">
            @if (!empty($telefono_valido) && $telefono_valido)
              <label class="block text-sm font-medium text-gray-800 dark:text-gray-100 mb-1">Número de teléfono</label>
              <input id="telefono" type="text" value="{{ $telefono_enmascarado }}"
                     class="w-full rounded-xl border border-purple-200/60 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2"
                     placeholder="9xxxxxxxx">
            @else
              <p class="text-sm text-red-600">No podemos validar con el número registrado.</p>
            @endif
          </div>

          {{-- Email --}}
          <div id="emailSection" class="hidden">
            @if (!empty($email))
              <label class="block text-sm font-medium text-gray-800 dark:text-gray-100 mb-1">Correo electrónico</label>
              <input id="email" type="email" value="{{ $email_enmascarado }}"
                     class="w-full rounded-xl border border-purple-200/60 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2"
                     placeholder="tucorreo@dominio.cl">
            @else
              <p class="text-sm text-red-600">No podemos validar con el correo registrado.</p>
            @endif
          </div>

          {{-- Examen --}}
          <div id="examenSection" class="hidden">
            <label class="block text-sm font-medium text-gray-800 dark:text-gray-100 mb-1">Seleccione un examen</label>
            <select id="examen" name="examen_id"
              class="w-full rounded-xl border border-purple-200/60 dark:border-gray-700 bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-2"
              disabled>
              <option value="">Seleccione…</option>
              @foreach ($prestaciones as $prestacion)
                <option value="{{ $prestacion->codigo }}">{{ $prestacion->nombre }}</option>
              @endforeach
            </select>
          </div>

          @error('validacion')
            <p class="text-sm text-red-600">{{ $message }}</p>
          @enderror

          <div class="pt-2 flex items-center gap-3">
            <button type="submit" id="btnValidar" disabled
              class="inline-flex items-center justify-center px-5 py-2 rounded-xl font-semibold bg-gray-200 text-gray-500 cursor-not-allowed">
              Validar
            </button>

            <button type="button" id="openModal"
              class="text-sm font-medium underline text-purple-800 hover:text-purple-900 dark:text-purple-300 dark:hover:text-purple-200">
              ¿Necesitas ayuda?
            </button>
          </div>
        </form>
      </div>
    </div>

    {{-- Modal de ayuda --}}
    <div id="helpModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center p-4">
      <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-md p-4 relative">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">¿Cómo iniciar sesión y obtener tus credenciales?</h3>
        <video id="onboardingVideo" class="w-full rounded-xl mt-3" controls>
          <source src="{{ asset('onboarding/quiero.registrarme.mp4') }}" type="video/mp4">
          Tu navegador no soporta el video.
        </video>
        <div class="mt-4 flex items-center justify-between">
          <button id="closeModal" class="px-4 py-2 rounded-xl font-semibold bg-red-600 text-white hover:bg-red-700">
            Cerrar
          </button>
          <button id="fullscreenBtn" class="px-4 py-2 rounded-xl font-semibold bg-purple-900 text-white hover:bg-purple-800">
            Agrandar
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- SCRIPTS --}}
<script>
  // Tema persistente
  (function() {
    const html = document.documentElement;
    const toggle = document.getElementById('themeToggle');
    if (localStorage.getItem('theme') === 'dark') {
      html.classList.add('dark');
      toggle.textContent = '☀️ Modo Claro';
    }
    toggle.addEventListener('click', () => {
      const isDark = html.classList.toggle('dark');
      localStorage.setItem('theme', isDark ? 'dark' : 'light');
      toggle.textContent = isDark ? '☀️ Modo Claro' : '🌙 Modo Oscuro';
    });
  })();

  // Lógica de validación (fix: exige examen seleccionado para habilitar)
  document.addEventListener('DOMContentLoaded', () => {
    const btnValidar = document.getElementById('btnValidar');
    const radios = document.querySelectorAll("input[name='validacion']");
    const telefonoInput = document.getElementById('telefono');
    const emailInput = document.getElementById('email');
    const examenInput = document.getElementById('examen');
    const tipoValidacion = document.getElementById('tipo_validacion');
    const valor = document.getElementById('valor');

    function show(id) {
      document.getElementById('telefonoSection').classList.add('hidden');
      document.getElementById('emailSection').classList.add('hidden');
      document.getElementById('examenSection').classList.add('hidden');
      document.getElementById(id).classList.remove('hidden');
    }

    function setEnabled(ok) {
      btnValidar.disabled = !ok;
      btnValidar.classList.toggle('cursor-not-allowed', !ok);
      btnValidar.classList.toggle('bg-gray-200', !ok);
      btnValidar.classList.toggle('text-gray-500', !ok);
      btnValidar.classList.toggle('bg-purple-900', ok);
      btnValidar.classList.toggle('text-white', ok);
      btnValidar.classList.toggle('hover:bg-purple-800', ok);
    }

    function checkInputs() {
      const telOk = telefonoInput && !telefonoInput.closest('.hidden') && telefonoInput.value.trim() !== '';
      const mailOk = emailInput && !emailInput.closest('.hidden') && emailInput.value.trim() !== '';
      const exOk = examenInput && !examenInput.closest('.hidden') && examenInput.value.trim() !== '';
      // Habilitar solo si alguno es válido (y en examen exige realmente una opción)
      setEnabled(telOk || mailOk || exOk);
    }

    radios.forEach(r => r.addEventListener('change', (e) => {
      const v = e.target.value; // telefono | email | examen
      tipoValidacion.value = v;

      if (v === 'telefono') {
        show('telefonoSection');
        examenInput.setAttribute('disabled', 'disabled');
      } else if (v === 'email') {
        show('emailSection');
        examenInput.setAttribute('disabled', 'disabled');
      } else {
        show('examenSection');
        examenInput.removeAttribute('disabled');
      }
      checkInputs();
    }));

    if (examenInput) examenInput.addEventListener('change', checkInputs);
    if (telefonoInput) telefonoInput.addEventListener('input', checkInputs);
    if (emailInput) emailInput.addEventListener('input', checkInputs);

    document.getElementById('validacionForm').addEventListener('submit', () => {
      if (tipoValidacion.value === 'telefono' && telefonoInput) {
        valor.value = telefonoInput.value.trim();
      } else if (tipoValidacion.value === 'email' && emailInput) {
        valor.value = emailInput.value.trim();
      } else if (tipoValidacion.value === 'examen' && examenInput) {
        valor.value = examenInput.value.trim();
      }
    });
  });

  // Modal ayuda
  (function() {
    const open = document.getElementById('openModal');
    const close = document.getElementById('closeModal');
    const modal = document.getElementById('helpModal');
    const video = document.getElementById('onboardingVideo');
    const fs = document.getElementById('fullscreenBtn');

    if (open) open.addEventListener('click', () => modal.classList.remove('hidden'));
    if (close) close.addEventListener('click', () => {
      modal.classList.add('hidden');
      if (video) video.pause();
    });
    if (fs) fs.addEventListener('click', () => {
      if (!video) return;
      if (video.requestFullscreen) video.requestFullscreen();
      else if (video.webkitRequestFullscreen) video.webkitRequestFullscreen();
      else if (video.mozRequestFullScreen) video.mozRequestFullScreen();
      else if (video.msRequestFullscreen) video.msRequestFullscreen();
    });
  })();
</script>
@endsection
