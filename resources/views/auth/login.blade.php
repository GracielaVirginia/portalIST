@extends('layouts.app')

@section('content')
    <!-- Overlay de carga -->
    <div id="loginOverlay"
        class="fixed inset-0 z-50 hidden items-center justify-center
            bg-white/85 dark:bg-gray-950/85 backdrop-blur-sm">
        <x-ui.skeleton-home class="w-full max-w-6xl mx-auto p-4" />
    </div>

    <div class="min-h-screen w-full bg-purple-100/70 dark:bg-gray-900/70">
        <div class="mx-auto max-w-7xl px-4 py-8 lg:py-12">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">

                {{-- LADO IZQUIERDO: Hero con imagen + textos --}}
                <section
                    class="lg:col-span-7 relative overflow-hidden rounded-3xl shadow-xl border border-purple-200/50 dark:border-purple-800/40">
                    {{-- Fondo dinámico según "seleccionada" --}}
                    <img src="{{ $imagenLoginUrl }}" alt="Fondo"
                        class="absolute inset-0 h-full w-full object-cover block dark:hidden" />

                    <img src="{{ $imagenLoginUrl }}" alt="Fondo oscuro"
                        class="absolute inset-0 h-full w-full object-cover hidden dark:block opacity-90" />

                    {{-- Capa de color para legibilidad --}}
                    <div class="absolute inset-0 bg-purple-900/70 dark:bg-gray-900/70"></div>

                    {{-- Contenido --}}
                    <div class="relative z-10 p-8 sm:p-10 lg:p-12 text-white">
                        <h2 class="text-3xl sm:text-4xl font-extrabold leading-tight">
                            Portal Salud IST,
                            <br><span class="text-teal-200">toda tu información médica</span>
                            <br>en un solo lugar.
                        </h2>

                        <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-3 max-w-lg">
                            <div
                                class="inline-flex items-center gap-2 rounded-2xl bg-white/10 backdrop-blur px-3 py-2 text-sm">
                                <span class="inline-grid place-items-center h-6 w-6 rounded-full bg-white/20">💬</span>
                                <span class="font-semibold">Citas y atención médica</span>
                            </div>
                            <div
                                class="inline-flex items-center gap-2 rounded-2xl bg-white/10 backdrop-blur px-3 py-2 text-sm">
                                <span class="inline-grid place-items-center h-6 w-6 rounded-full bg-white/20">🩺</span>
                                <span class="font-semibold">Salud preventiva</span>
                            </div>
                            <div
                                class="inline-flex items-center gap-2 rounded-2xl bg-white/10 backdrop-blur px-3 py-2 text-sm">
                                <span
                                    class="inline-grid place-items-center h-6 w-6 rounded-full bg-white/20">👨‍👩‍👧</span>
                                <span class="font-semibold">Gestión de tu familia</span>
                            </div>
                            <div
                                class="inline-flex items-center gap-2 rounded-2xl bg-white/10 backdrop-blur px-3 py-2 text-sm">
                                <span class="inline-grid place-items-center h-6 w-6 rounded-full bg-white/20">📄</span>
                                <span class="font-semibold">Recetas y órdenes</span>
                            </div>
                        </div>

                        {{-- <div class="mt-10">
                            <a href="{{ route('portal.conoce-mas') }}"
                                class="inline-flex items-center gap-2 text-sm font-semibold underline underline-offset-4 decoration-teal-200 hover:opacity-90">
                                Conoce más del Portal
                                <span aria-hidden="true">↗</span>
                            </a>
                        </div> --}}
                    </div>
                </section>

                {{-- LADO DERECHO: Card de Login --}}
                <section class="lg:col-span-5 flex">
                    <div
                        class="relative w-full m-auto rounded-3xl border border-purple-200/60 dark:border-purple-800/60 bg-white/80 dark:bg-gray-800/70 backdrop-blur-md shadow-xl">
                        <div class="p-6 sm:p-8">

                            {{-- Logo + título --}}
                            <div class="flex items-center justify-center gap-2">
                                <div
                                    class="inline-grid place-items-center h-14 w-14 rounded-2xl overflow-hidden shadow-sm border border-purple-300/40 dark:border-purple-700/40 bg-white dark:bg-gray-800">
                                    <img src="{{ asset('favicon.ico') }}" alt="Logo IST" class="h-7 w-7 object-contain" />
                                </div>

                                <div class="text-left">
                                    <h1
                                        class="text-xl sm:text-2xl font-extrabold text-purple-900 dark:text-purple-100 leading-none">
                                        Iniciar sesión
                                    </h1>

                                </div>
                            </div>
@include('components.login.switch_rut_pasaporte')
                            {{-- Formulario --}}
                            <form method="POST" id="formLogin" action="{{ route('login.attempt') }}" class="mt-6 space-y-5"
                                novalidate>
                                @csrf

                                {{-- Config verificación RUT (no cambio IDs) --}}
                                {{-- <div id="verifConfig" data-url="{{ route('verificar-rut') }}"
                                    data-csrf="{{ csrf_token() }}"></div> --}}
                                {{-- Config verificación RUT/Pasaporte (no cambio IDs) --}}
                                <div id="verifConfig" data-url="{{ route('verificar-rut') }}"
                                    data-url-pasaporte="{{ route('verificar-pasaporte') }}" data-csrf="{{ csrf_token() }}">
                                </div>
                                {{-- Campo RUT --}}
                                <div>
                                    <label for="rut"
                                        class="block text-sm font-semibold text-purple-900 dark:text-purple-200">Ingresa tu
                                        RUT</label>
                                    <div class="mt-1 relative">
                                        <input id="rut" name="rut" type="text" value="{{ old('rut') }}"
                                            placeholder="11.111.111-1" maxlength="14" inputmode="text"
                                            autocomplete="username" required
                                            class="w-full rounded-xl border border-purple-300/70 dark:border-purple-700/70 bg-white dark:bg-gray-900
                           text-gray-900 dark:text-gray-100 placeholder:text-gray-400 dark:placeholder:text-gray-400
                           px-4 py-3 pr-12 shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500" />
                                        <button type="button"
                                            class="absolute inset-y-0 right-2 my-auto h-9 w-9 grid place-items-center rounded-lg
                                 text-purple-700 dark:text-purple-300 hover:bg-purple-100/60 dark:hover:bg-purple-900/40"
                                            title="Ayuda RUT" onclick="openHelp()">❔</button>
                                    </div>
                                    <p id="rutFeedback" class="text-xs mt-1 text-gray-600 dark:text-gray-300"></p>
                                </div>

                                {{-- Campo Password --}}
                                <div>
                                    <label for="password"
                                        class="block text-sm font-semibold text-purple-900 dark:text-purple-200">Ingresa tu
                                        contraseña</label>
                                    <div class="mt-1 relative">
                                        <input id="password" name="password" type="password" placeholder="********"
                                            autocomplete="current-password" disabled required
                                            class="w-full rounded-xl border border-purple-300/70 dark:border-purple-700/70 bg-white dark:bg-gray-900
                           text-gray-900 dark:text-gray-100 placeholder:text-gray-400 dark:placeholder:text-gray-400
                           px-4 py-3 pr-12 shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500" />
                                        <button type="button" id="togglePassword"
                                            class="absolute inset-y-0 right-2 my-auto h-9 w-9 grid place-items-center rounded-lg
                                 text-purple-700 dark:text-purple-300 hover:bg-purple-100/60 dark:hover:bg-purple-900/40"
                                            aria-label="Mostrar/Ocultar contraseña" disabled>👁️</button>
                                    </div>
                                    @error('password')
                                        <p class="text-xs text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Botón enviar --}}
                                <div class="pt-2">
                                    <button id="btnLogin" type="submit" disabled
                                        class="w-full inline-flex items-center justify-center gap-2 rounded-2xl px-5 py-3 font-semibold
                               bg-purple-900 text-white shadow-sm hover:bg-purple-800 disabled:opacity-70 disabled:cursor-not-allowed">
                                        Ingresar
                                    </button>
                                </div>
                            </form>
                            {{-- ===== FORM B: Login por Pasaporte (inicialmente oculto) ===== --}}
                            <form method="POST" id="formLoginPasaporte" action="{{ route('login.attempt.pasaporte') }}"
                                class="mt-6 space-y-5 hidden" novalidate aria-hidden="true">
                                @csrf

                                {{-- Campo Pasaporte --}}
                                <div>
                                    <label for="pasaporte"
                                        class="block text-sm font-semibold text-purple-900 dark:text-purple-200">
                                        Ingresa tu Pasaporte
                                    </label>
                                    <div class="mt-1 relative">
                                        <input id="pasaporte" name="pasaporte" type="text"
                                            value="{{ old('pasaporte') }}" placeholder="AB1234567" maxlength="20"
                                            inputmode="text" autocomplete="username" required
                                            class="w-full rounded-xl border border-purple-300/70 dark:border-purple-700/70 bg-white dark:bg-gray-900
                    text-gray-900 dark:text-gray-100 placeholder:text-gray-400 dark:placeholder:text-gray-400
                    px-4 py-3 pr-12 shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500" />
                                        <button type="button" id="toggleHelpPasaporte"
                                            class="absolute inset-y-0 right-2 my-auto h-9 w-9 grid place-items-center rounded-lg
                     text-purple-700 dark:text-purple-300 hover:bg-purple-100/60 dark:hover:bg-purple-900/40"
                                            title="Ayuda Pasaporte" onclick="openHelp()">❔</button>
                                    </div>
                                    <p id="pasaporteFeedback" class="text-xs mt-1 text-gray-600 dark:text-gray-300"></p>
                                </div>

                                {{-- Campo Password (pasaporte) --}}
                                <div>
                                    <label for="passworPasaporte"
                                        class="block text-sm font-semibold text-purple-900 dark:text-purple-200">
                                        Ingresa tu contraseña
                                    </label>
                                    <div class="mt-1 relative">
                                        <input id="passworPasaporte" name="passworPasaporte" type="password"
                                            placeholder="********" autocomplete="current-password" disabled required
                                            class="w-full rounded-xl border border-purple-300/70 dark:border-purple-700/70 bg-white dark:bg-gray-900
                    text-gray-900 dark:text-gray-100 placeholder:text-gray-400 dark:placeholder:text-gray-400
                    px-4 py-3 pr-12 shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500" />
                                        <button type="button" id="togglePasswordPasaporte"
                                            class="absolute inset-y-0 right-2 my-auto h-9 w-9 grid place-items-center rounded-lg
                     text-purple-700 dark:text-purple-300 hover:bg-purple-100/60 dark:hover:bg-purple-900/40"
                                            aria-label="Mostrar/Ocultar contraseña" disabled>👁️</button>
                                    </div>
                                </div>

                                {{-- Botón enviar (pasaporte) --}}
                                <div class="pt-2">
                                    <button id="btnLoginPasaporte" type="submit" disabled
                                        class="px-3 py-1.5 text-sm rounded-lg bg-gray-200 text-gray-500 cursor-not-allowed border border-ring transition">
                                        Ingresar
                                    </button>
                                </div>
                            </form>

                            {{-- Acciones secundarias --}}
                            <div class="mt-5 flex flex-col items-center gap-2 text-sm">
                                <button type="button" onclick="openModalRut()"
                                    class="font-semibold text-purple-900 hover:text-purple-700 dark:text-purple-200 dark:hover:text-purple-100 underline underline-offset-4">
                                    ¿Olvidaste tu contraseña?
                                </button>

                                {{-- <a href="#"
                                    class="text-gray-700 dark:text-gray-300 hover:text-purple-800 dark:hover:text-purple-200 underline underline-offset-4">
                                    ¿Aún no tienes cuenta? Regístrate aquí
                                </a> --}}
                            </div>
                        </div>

                        {{-- Bloque informativo (mini) --}}
                        <div
                            class="mt-6 rounded-2xl border border-purple-200/60 dark:border-purple-800/60 bg-purple-50/70 dark:bg-purple-950/30 p-4">
                            <p class="text-xs leading-relaxed text-purple-900/90 dark:text-purple-100">
                                Crea tu cuenta y accede a beneficios del Portal Salud IST. Conoce cómo registrarte y
                                comienza a gestionar tus atenciones, recetas y tu grupo familiar.
                            </p>
                        </div>
                    </div>
            </div>
            </section>

            <x-portal.home-section />


        </div>
    </div>

    {{-- ========== MODAL: Olvidé mi contraseña (mismo ID/nombres) ========== --}}
    <div id="modalRut" class="fixed inset-0 hidden items-center justify-center bg-black/60 z-50 backdrop-blur-sm">
        <div
            class="bg-white dark:bg-gray-900 p-6 rounded-2xl shadow-2xl w-96
           text-gray-900 dark:text-gray-100 border border-purple-200 dark:border-purple-800">

            <h2 class="text-lg font-bold text-center text-purple-900 dark:text-purple-200">
                Olvidé mi Contraseña
            </h2>

            <p class="text-sm mt-3 text-center leading-relaxed text-gray-700 dark:text-gray-300">
                Dirígete a la
                <span class="font-semibold text-purple-800 dark:text-purple-300">Clínica IST</span>
                o comunícate con el
            </p>

            <div class="mt-3 flex justify-center">
                {{-- Botón que cambia texto al hacer hover y redirige a soporte.create --}}
                <a href="{{ route('soporte.create') }}"
                    class="px-4 py-1.5 text-sm font-semibold rounded-lg border border-purple-700 text-purple-800
                dark:text-purple-200 hover:bg-purple-800 hover:text-white
                transition-all duration-300"
                    onmouseover="this.innerText='Ayuda'" onmouseout="this.innerText='Administrador'">
                    Administrador
                </a>
            </div>

            <div class="mt-6 flex justify-center">
                <button onclick="closeModalRut()"
                    class="px-5 py-2 rounded-xl font-semibold text-white
                     bg-purple-900 hover:bg-purple-800 dark:bg-purple-700 dark:hover:bg-purple-600
                     focus:outline-none focus:ring-2 focus:ring-purple-400 focus:ring-offset-1">
                    Cerrar
                </button>
            </div>
        </div>
    </div>


    {{-- ========== MODAL: Ayuda (mismo ID/nombres) ========== --}}
    <div id="helpModal" class="fixed inset-0 hidden items-center justify-center bg-black/60 z-50">
        <div
            class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-2xl w-[22rem] text-gray-900 dark:text-gray-100 border border-purple-200 dark:border-purple-800">
            <h2 class="text-lg font-bold text-center">Ayuda</h2>
            <p class="text-sm mt-3 text-justify">
                Si tienes problemas para ingresar, verifica tu RUT y solicita soporte al administrador del sistema.
            </p>
            <div class="mt-5 flex justify-center">
                <button onclick="closeHelp()"
                    class="px-5 py-2 rounded-xl font-semibold bg-purple-900 text-white hover:bg-purple-800">Cerrar</button>
            </div>
        </div>
    </div>

    {{-- Scripts mínimos para abrir/cerrar modales (no cambio nombres) --}}
    <script>
        function openModalRut() {
            const m = document.getElementById('modalRut');
            m.classList.remove('hidden');
            m.classList.add('flex');
        }

        function closeModalRut() {
            const m = document.getElementById('modalRut');
            m.classList.add('hidden');
            m.classList.remove('flex');
        }

        function openHelp() {
            const m = document.getElementById('helpModal');
            m.classList.remove('hidden');
            m.classList.add('flex');
        }

        function closeHelp() {
            const m = document.getElementById('helpModal');
            m.classList.add('hidden');
            m.classList.remove('flex');
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        (() => {
            let fired = false;
            const SOPORTE = '/contacto'; // cambia si quieres WhatsApp/mailto

            function fireAlert(msg) {
                if (fired) return;
                fired = true;
                console.log('[bloqueado] detectado en respuesta JSON');
                Swal.fire({
                    icon: 'error',
                    title: 'Cuenta bloqueada',
                    text: msg || 'Paciente Bloqueado. Comunícate con el Administrador',
                    confirmButtonText: 'Contactar soporte',
                    confirmButtonColor: '#7c3aed',
                    allowOutsideClick: false,
                    backdrop: 'rgba(0,0,0,0.6)'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // 🔗 Redirige directamente a la ruta soporte.create
                        window.location.href = "{{ route('soporte.create') }}";
                    }
                });
            }

            // --- Intercepta window.fetch ---
            if (window.fetch) {
                const _fetch = window.fetch.bind(window);
                window.fetch = async (...args) => {
                    const res = await _fetch(...args);
                    try {
                        const ct = res.headers.get('content-type') || '';
                        if (ct.includes('application/json')) {
                            const clone = res.clone();
                            const data = await clone.json();
                            console.log('[bloqueado][fetch] JSON:', data);
                            if (data && data.bloqueado === true) fireAlert(data.message);
                        }
                    } catch (e) {
                        /* silencio */ }
                    return res;
                };
            }

            // --- Intercepta XMLHttpRequest ---
            (function() {
                const _open = XMLHttpRequest.prototype.open;
                const _send = XMLHttpRequest.prototype.send;

                XMLHttpRequest.prototype.open = function(method, url, async, user, pass) {
                    this.__url = url;
                    return _open.apply(this, arguments);
                };

                XMLHttpRequest.prototype.send = function(body) {
                    this.addEventListener('load', function() {
                        try {
                            const ct = this.getResponseHeader('content-type') || '';
                            if (ct.includes('application/json')) {
                                const data = JSON.parse(this.responseText);
                                console.log('[bloqueado][xhr] JSON:', data, '->', this.__url);
                                if (data && data.bloqueado === true) fireAlert(data.message);
                            }
                        } catch (e) {
                            /* silencio */ }
                    });
                    return _send.apply(this, arguments);
                };
            })();
        })();
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const rutInput = document.getElementById('rut');
            if (rutInput) {
                rutInput.focus();
                rutInput.select(); // opcional: selecciona el texto si ya hay algo escrito
            }
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('formLogin');
            const overlay = document.getElementById('loginOverlay');
            if (!form || !overlay) return;

            form.addEventListener('submit', () => {
                // Muestra overlay
                overlay.classList.remove('hidden');
                overlay.classList.add('flex');

                // 1) Deshabilita SOLO lo necesario (NO ocultos / NO _token)
                const controls = form.querySelectorAll(
                    'button, input:not([type="hidden"]):not([name="_token"]), select, textarea'
                );
                controls.forEach(el => {
                    // para inputs usa readonly mejor que disabled, así igual se envían si quedara alguno visible
                    if (el.tagName === 'INPUT' || el.tagName === 'TEXTAREA') {
                        el.setAttribute('readonly', 'readonly');
                    } else {
                        el.setAttribute('disabled', 'disabled');
                    }
                });

                // 2) Cambia el texto del submit
                const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
                if (submitBtn) {
                    submitBtn.dataset.originalText = submitBtn.innerHTML;
                    submitBtn.innerHTML = `
        <span class="inline-flex items-center gap-2">
          <span class="h-4 w-4 rounded-full border-2 border-white border-t-transparent animate-spin"></span>
          Ingresando…
        </span>`;
                    submitBtn.classList.add('opacity-80', 'cursor-wait');
                }

                // Importante: NO usar preventDefault en este flujo (postback).
                // Y no deshabilitar el _token ni otros hidden.
            });
        });
    </script>
@endsection
