@extends('layouts.app')

@section('title', 'Validación — Creando cuenta')

@section('content')
<div class="min-h-[88vh] bg-gradient-to-b from-violet-100 to-white dark:from-gray-900 dark:to-gray-900">
  <div class="max-w-3xl mx-auto px-6 py-8">

    {{-- Topbar: título + botón Salir --}}
    <div class="flex items-center justify-between mb-4">
      <div class="flex items-center gap-3">
        <div class="flex items-center justify-center w-12 h-12 rounded-2xl bg-violet-900 text-white shadow">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12 12a5 5 0 10-5-5 5 5 0 005 5zm0 2c-4 0-7 2-7 4v2h14v-2c0-2-3-4-7-4z"/>
          </svg>
        </div>
        <div>
          <h1 class="text-2xl md:text-3xl font-extrabold text-violet-900 dark:text-violet-200">
            Crear tu cuenta en el portal
          </h1>
          <p class="text-sm text-violet-900/80 dark:text-gray-300">
            Completa tus datos y verifica tu correo con un código de 6 dígitos.
          </p>
        </div>
      </div>

      {{-- Botón Salir (cierra sesión con confirmación) --}}
      <button type="button" id="btnSalirTop"
              class="inline-flex items-center gap-2 rounded-xl bg-gray-200 text-gray-800 dark:bg-gray-800 dark:text-gray-100 px-4 py-2 text-sm font-semibold hover:bg-gray-300 dark:hover:bg-gray-700">
        Salir
      </button>
    </div>

    <div class="border-t border-violet-200/70 dark:border-gray-800 mb-4"></div>

    {{-- flashes --}}
    @if (session('ok'))
      <div class="mb-4 rounded-lg bg-green-100 dark:bg-green-900/40 text-green-800 dark:text-green-200 px-4 py-3">
        {{ session('ok') }}
      </div>
    @endif

    {{-- FORM --}}
    <form method="POST" action="{{ route('validacion.cuenta.store') }}" class="space-y-4" id="formCrearCuenta">
      @csrf

      {{-- Sección 1: Identificación (requerida) --}}
      <section class="bg-white/80 dark:bg-gray-900/60 p-4 rounded-2xl ring-1 ring-black/5">
        <h2 class="text-base font-semibold text-violet-900 dark:text-violet-100 mb-3">Identificación</h2>

        {{-- Fila 1: RUT, Nombre, Fecha de nacimiento --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label for="rut" class="block text-sm font-semibold text-content">RUT</label>
            <input id="rut" name="rut" type="text" inputmode="text"
                   value="{{ old('rut', auth()->user()->rut ?? '') }}"
                   maxlength="12" placeholder="11111111-1"
                   @if(auth()->check() && auth()->user()->rut) readonly @endif
                   class="mt-1 w-full rounded-xl border border-violet-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-violet-500">
            @error('rut') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
          </div>

          <div>
            <label for="name" class="block text-sm font-semibold text-content">Nombre completo</label>
            <input id="name" name="name" type="text" value="{{ old('name', auth()->user()->name ?? '') }}"
                   class="mt-1 w-full rounded-xl border border-violet-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-violet-500">
            @error('name') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
          </div>

          <div>
            <label for="fecha_nacimiento" class="block text-sm font-semibold text-content">Fecha de nacimiento</label>
            <input id="fecha_nacimiento" name="fecha_nacimiento" type="date" value="{{ old('fecha_nacimiento') }}"
                   class="mt-1 w-full rounded-xl border border-violet-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-violet-500">
            @error('fecha_nacimiento') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
          </div>
        </div>

        {{-- Fila 2: Edad (auto), Sexo --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-3">
          <div>
            <label for="edad_auto" class="block text-sm font-semibold text-content">Edad</label>
            <input id="edad_auto" type="text" readonly placeholder="—"
                   class="mt-1 w-full rounded-xl border border-violet-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 px-3 py-2">
          </div>

          <div>
            <label for="sexo" class="block text-sm font-semibold text-content">Sexo</label>
            <select id="sexo" name="sexo"
                    class="mt-1 w-full rounded-xl border border-violet-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-violet-500">
              <option value="">— Selecciona —</option>
              <option value="F" @selected(old('sexo')==='F')>Femenino</option>
              <option value="M" @selected(old('sexo')==='M')>Masculino</option>
            </select>
            @error('sexo') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
          </div>

          <div class="hidden md:block"></div>
        </div>
      </section>

      {{-- Sección 2: Contacto --}}
      <section class="bg-white/80 dark:bg-gray-900/60 p-4 rounded-2xl ring-1 ring-black/5">
        <h2 class="text-base font-semibold text-violet-900 dark:text-violet-100 mb-3">Contacto</h2>

        {{-- Email + Enviar código --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div class="md:col-span-2">
            <label for="email" class="block text-sm font-semibold text-content">Email</label>
            <div class="flex gap-2">
              <input id="email" name="email" type="email"
                     value="{{ old('email', auth()->user()->email ?? '') }}" autocomplete="email"
                     class="mt-1 w-full rounded-xl border border-violet-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-violet-500">
              <button type="button" id="btnSendCode"
                      class="mt-1 shrink-0 px-3 py-2 rounded-xl bg-violet-900 text-white text-sm font-semibold hover:bg-violet-800">
                Enviar código
              </button>
            </div>
            @error('email') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            <p id="codeHint" class="mt-1 text-xs text-gray-600 dark:text-gray-400 hidden">
              Te enviamos un código de 6 dígitos. Revísalo en tu correo.
            </p>
            <p id="codeErr" class="mt-1 text-xs text-red-600 dark:text-red-400 hidden"></p>
          </div>

          <div>
            <label for="telefono" class="block text-sm font-semibold text-content">Teléfono</label>
            <input id="telefono" name="telefono" type="tel" value="{{ old('telefono') }}" placeholder="+56 9 1234 5678"
                   class="mt-1 w-full rounded-xl border border-violet-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-violet-500">
            @error('telefono') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
          </div>
        </div>

        {{-- Dirección --}}
        <div class="mt-3">
          <label for="direccion" class="block text-sm font-semibold text-content">Dirección</label>
          <input id="direccion" name="direccion" type="text" value="{{ old('direccion') }}" placeholder="Calle, número, comuna"
                 class="mt-1 w-full rounded-xl border border-violet-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-violet-500">
          @error('direccion') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
        </div>
      </section>

      {{-- Sección 3 (opcional): Información clínica en dropdown --}}
      <details class="group bg-white/80 dark:bg-gray-900/60 p-4 rounded-2xl ring-1 ring-black/5">
        <summary class="cursor-pointer list-none flex items-center justify-between">
          <h2 class="text-base font-semibold text-violet-900 dark:text-violet-100">Información clínica (opcional)</h2>
          <span class="text-xs text-gray-500 group-open:rotate-180 transition">▾</span>
        </summary>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-3">
          <div>
            <label for="grupo_sanguineo" class="block text-sm font-semibold text-content">Grupo sanguíneo</label>
            <select id="grupo_sanguineo" name="grupo_sanguineo"
                    class="mt-1 w-full rounded-xl border border-violet-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-violet-500">
              <option value="">— Selecciona —</option>
              @foreach (['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $gs)
                <option value="{{ $gs }}" @selected(old('grupo_sanguineo')===$gs)>{{ $gs }}</option>
              @endforeach
            </select>
            @error('grupo_sanguineo') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
          </div>

          <div class="md:col-span-2">
            <label for="alergias" class="block text-sm font-semibold text-content">Alergias</label>
            <textarea id="alergias" name="alergias" rows="3" placeholder="Ej.: Penicilina, mariscos, etc."
                      class="mt-1 w-full rounded-xl border border-violet-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-violet-500">{{ old('alergias') }}</textarea>
            @error('alergias') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-3">
          <div>
            <label for="telefono_emergencia" class="block text-sm font-semibold text-content">Teléfono de emergencia</label>
            <input id="telefono_emergencia" name="telefono_emergencia" type="tel" value="{{ old('telefono_emergencia') }}" placeholder="+56 9 ..."
                   class="mt-1 w-full rounded-xl border border-violet-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-violet-500">
            @error('telefono_emergencia') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
          </div>
          <div class="md:col-span-2"></div>
        </div>
      </details>

      {{-- Sección 4: Verificación por correo (solo input, sin botón finalizar aquí) --}}
      <section class="bg-white/80 dark:bg-gray-900/60 p-4 rounded-2xl ring-1 ring-black/5">
        <h2 class="text-base font-semibold text-violet-900 dark:text-violet-100 mb-3">Verificación por correo</h2>
        <div>
          <label for="verification_code" class="block text-sm font-semibold text-content">Código de verificación</label>
          <input id="verification_code" name="verification_code" type="text" inputmode="numeric" maxlength="6"
                 placeholder="••••••"
                 class="mt-1 w-full rounded-xl border border-violet-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-violet-500">
          @error('verification_code') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
          <p class="mt-1 text-xs text-gray-600 dark:text-gray-400">
            Primero presiona “Enviar código” en la sección de Email. Luego ingrésalo aquí.
          </p>
        </div>
      </section>

      {{-- Sección 5: Consentimientos --}}
      <section class="bg-white/80 dark:bg-gray-900/60 p-4 rounded-2xl ring-1 ring-black/5">
        <h2 class="text-base font-semibold text-violet-900 dark:text-violet-100 mb-3">Consentimientos</h2>
        <label class="inline-flex items-start gap-2">
          <input type="checkbox" name="accept_terms" value="1" class="mt-1">
          <span class="text-sm text-gray-700 dark:text-gray-200">
            Acepto los <a href="{{ url('/terminos') }}" class="underline hover:text-purple-700">Términos</a> y la
            <a href="{{ url('/privacidad') }}" class="underline hover:text-purple-700">Política de privacidad</a>.
          </span>
        </label>
        @error('accept_terms') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
      </section>

      {{-- Sección 6: Acciones --}}
      <div class="flex items-center justify-end gap-3 pt-2">
        <button type="button" id="btnCancelar"
                class="inline-flex items-center gap-2 rounded-xl bg-gray-200 text-gray-800 dark:bg-gray-800 dark:text-gray-100 px-4 py-2 text-sm font-semibold hover:bg-gray-300 dark:hover:bg-gray-700">
          Cancelar
        </button>

        <button type="submit"
                class="inline-flex items-center gap-2 rounded-xl bg-violet-900 text-white px-5 py-2.5 text-sm font-semibold hover:bg-violet-800">
          Finalizar
        </button>
      </div>
    </form>

    {{-- Form oculto para logout --}}
    <form id="formLogout" method="POST" action="{{ route('logout') }}" class="hidden">
      @csrf
    </form>
  </div>
</div>

@push('scripts')
{{-- SweetAlert2 (CDN) --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  (function(){
    // ---- Calcular edad según fecha de nacimiento ----
    const fnac = document.getElementById('fecha_nacimiento');
    const edad = document.getElementById('edad_auto');
    function calcEdad(isoDate){
      if(!isoDate) return '';
      const hoy = new Date();
      const n = new Date(isoDate + 'T00:00:00');
      let e = hoy.getFullYear() - n.getFullYear();
      const m = hoy.getMonth() - n.getMonth();
      if (m < 0 || (m === 0 && hoy.getDate() < n.getDate())) e--;
      return (e >= 0 && e < 130) ? e + ' años' : '';
    }
    if (fnac && edad) {
      edad.value = calcEdad(fnac.value);
      fnac.addEventListener('change', () => { edad.value = calcEdad(fnac.value); });
    }

    // ---- Enviar código por email (AJAX) ----
    const btn = document.getElementById('btnSendCode');
    const emailInput = document.getElementById('email');
    const hint = document.getElementById('codeHint');
    const err  = document.getElementById('codeErr');

    if (btn && emailInput) {
      btn.addEventListener('click', async function(){
        if (err){ err.classList.add('hidden'); err.textContent = ''; }
        const email = (emailInput.value || '').trim();
        if (!email) {
          if (err){ err.textContent = 'Ingresa un email para enviar el código.'; err.classList.remove('hidden'); }
          return;
        }
        btn.disabled = true; const oldTxt = btn.textContent; btn.textContent = 'Enviando...';
        try {
          const res = await fetch('{{ route('validacion.cuenta.codigo') }}', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': '{{ csrf_token() }}',
              'Accept': 'application/json'
            },
            body: JSON.stringify({ email })
          });
          const data = await res.json();
          if (data.ok) {
            if (hint) hint.classList.remove('hidden');
          } else {
            if (err){ err.textContent = data.message || 'No se pudo enviar el código.'; err.classList.remove('hidden'); }
          }
        } catch (e) {
          if (err){ err.textContent = 'Error de red enviando el código.'; err.classList.remove('hidden'); }
        } finally {
          btn.disabled = false; btn.textContent = oldTxt;
        }
      });
    }

    // ---- Confirmar salida (Cancelar y Salir) ----
    const formLogout = document.getElementById('formLogout');
    function confirmarSalida() {
      Swal.fire({
        title: '¿Deseas salir?',
        text: 'Aún no has finalizado la creación de tu cuenta.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, salir',
        cancelButtonText: 'Continuar aquí',
        reverseButtons: true
      }).then((r) => {
        if (r.isConfirmed && formLogout) formLogout.submit();
      });
    }
    const btnCancelar = document.getElementById('btnCancelar');
    const btnSalirTop = document.getElementById('btnSalirTop');
    if (btnCancelar) btnCancelar.addEventListener('click', confirmarSalida);
    if (btnSalirTop) btnSalirTop.addEventListener('click', confirmarSalida);
  })();
</script>
@endpush
@endsection
