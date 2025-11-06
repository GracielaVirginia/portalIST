{{-- resources/views/portal/home.blade.php --}}
@extends('layouts.app', ['topbar' => true, 'navbar' => true])

@section('title', 'Portal de Salud — Inicio')

@section('content')
    @php
        // Fallbacks por si el controlador aún trae placeholders
        $paciente = $paciente ?? [
            'nombre' => 'Paciente',
            'rut' => null,
            'sexo' => null,
            'edad' => null,
            'idioma' => 'es',
            'cronico' => false,
            'condiciones' => [],
        ];
        $kpis = $kpis ?? ['proximas_citas' => 0, 'resultados_disponibles' => 0, 'ordenes' => 0, 'alertas' => 0];
        $sidebar = $sidebar ?? ['resultados' => ['total' => 0, 'por_especialidad' => []]];
        $itemsRecientes = $itemsRecientes ?? [];
        $seriesControles = $seriesControles ?? ['tension' => [], 'glucosa' => [], 'peso' => []];
        $sugerencias = $sugerencias ?? [];
        $noticia = $noticia ?? null;
    @endphp

    <div x-data="{ modalResultados: false, modalSalud: false }" class="container mx-auto px-4 sm:px-6 lg:px-8 pt-4 pb-6 overflow-x-hidden">

        @if (Auth::check() && Auth::user()->password_needs_change)
            <x-security.force-password-modal />
        @endif

        @include('components.calificacion')

        {{-- ===================== HEADER PACIENTE ===================== --}}
        <div id="tour-header">
            <x-portal.panel-header :paciente="$paciente" onOrganizar="{{ route('portal.home') }}#organizar" />
        </div>

        {{-- ===================== KPIs ===================== --}}
        <div class="mt-6" id="tour-kpis">
            <x-portal.kpis :kpis="$kpis" />
        </div>

        {{-- ===================== LAYOUT PRINCIPAL ===================== --}}
        <div class="mt-6 grid grid-cols-1 lg:grid-cols-12 gap-4">
            {{-- Sidebar --}}
            <div class="lg:col-span-3 space-y-3" id="tour-sidebar">
                <x-portal.sidebar :resultados="$sidebar['resultados']" />
            </div>

            {{-- Contenido principal --}}
            <div class="lg:col-span-9 space-y-4">
                <div id="tour-noticia">
                    @if ($noticia)
                        <x-portal.widget-noticia :noticia="$noticia" />
                    @else
                        <x-portal.widget-noticia />
                    @endif
                </div>
            </div>
        </div>

        {{-- ===================== MODALES ===================== --}}
        <div x-show="modalResultados" x-cloak
            class="fixed inset-0 z-40 bg-black/40 backdrop-blur-sm flex items-center justify-center p-4"
            @keydown.escape.window="modalResultados=false">
            <div
                class="w-full max-w-4xl rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 shadow-xl overflow-hidden">
                <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Resultados recientes</h3>
                    <button
                        class="rounded-lg px-2 py-1 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800"
                        @click="modalResultados=false">Cerrar</button>
                </div>
                <div class="p-4">
                    <x-portal.widget-resultados-recientes :items="$itemsRecientes" />
                    <div class="mt-4 text-right">
                        <a href="{{ route('portal.resultados.index') }}"
                            class="inline-flex items-center gap-2 rounded-xl border border-purple-900/20 dark:border-purple-300/20
                  bg-purple-900 text-white hover:opacity-90 px-4 py-2 text-sm font-semibold">
                            Ver todos
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div x-show="modalSalud" x-cloak
            class="fixed inset-0 z-40 bg-black/40 backdrop-blur-sm flex items-center justify-center p-4"
            @keydown.escape.window="modalSalud=false">
            <div
                class="w-full max-w-5xl rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 shadow-xl overflow-hidden">
                <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Calendario y sugerencias</h3>
                    <button
                        class="rounded-lg px-2 py-1 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800"
                        @click="modalSalud=false">Cerrar</button>
                </div>
            </div>
        </div>

        {{-- ===================== BOTÓN FLOTANTE TOUR ===================== --}}
        <button id="btnTourHome"
            class="fixed z-50 left-1/2 -translate-x-1/2 top-6
         rounded-2xl shadow-lg bg-purple-700 hover:bg-purple-800
         text-white px-6 py-4 text-base md:text-lg font-semibold
         focus:outline-none focus:ring-4 focus:ring-purple-300"
            aria-label="¿Cómo usar esta página?">
            ¿Cómo usar esta página?
        </button>

        {{-- ===================== INTRO.JS ===================== --}}
        <link rel="stylesheet" href="https://unpkg.com/intro.js/minified/introjs.min.css">
        <script src="https://unpkg.com/intro.js/minified/intro.min.js"></script>

        <script>
            (function() {
                const KEY = 'portal_tour_done_home';

                function buildSteps() {
                    const steps = [];
                    steps.push({
                        intro: "¡Bienvenida/o al <b>Portal Salud IST</b>! Te mostraremos en pocos pasos dónde está todo."
                    });

                    if (document.querySelector('#tour-header')) {
                        steps.push({
                            element: '#tour-header',
                            intro: "Aquí está tu <b>ficha de paciente</b>: nombre, RUT, edad y condiciones. También puedes acceder a tus controles y editar tu perfil."
                        });
                    }
                    // ===== Paso: Botón real Mis controles (opcional, refuerza) =====
                    if (document.querySelector('#btnMisControles')) {
                        steps.push({
                            element: '#btnMisControles',
                            position: 'bottom',
                            intro: `
      <div>
Aquí está <b>Mis controles</b>. Presiona para agregar Peso, Tensión o Glucosa.        <ul style="margin-top:8px; padding-left:20px; line-height:1.4;">
          <li>Registra tu peso, tensión y glucosa fácilmente.</li>
          <li>Comparte tus resultados con tu médico.</li>
          <li>Pide una hora con un especialista si lo necesitas.</li>
        </ul>
      </div>
    `
                        });
                    }
                    if (document.querySelector('#btnEditarPerfil')) {
                        steps.push({
                            element: '#btnEditarPerfil',
                            position: 'bottom',
                            intro: `
      <div>
        Aquí puedes <b>editar tu perfil</b> personal.
        <ul style="margin-top:8px; padding-left:20px; line-height:1.4;">
          <li>Actualiza tus <b>datos personales</b> como correo o teléfono.</li>
          <li><b>Cambia tu contraseña</b> para mantener tu cuenta segura.</li>
          <li><b>Elimina tu cuenta</b> de forma permanente si ya no deseas usar el portal.</li>
        </ul>
      </div>
    `
                        });
                    }

                    if (document.querySelector('#tour-kpis')) {
                        steps.push({
                            element: '#tour-kpis',
                            position: 'bottom',
                            intro: "Estos son tus <b>indicadores</b>: próximas citas, resultados disponibles, licencias y alertas."
                        });
                    }

                    if (document.querySelector('#tour-sidebar')) {
                        steps.push({
                            element: '#tour-sidebar',
                            position: 'right',
                            intro: "En este menú verás <b>todos tus resultados</b> y accesos por especialidad. Es el lugar más usado."
                        });
                    }

                    if (document.querySelector('#tour-noticia')) {
                        steps.push({
                            element: '#tour-noticia',
                            position: 'bottom',
                            intro: "Aquí mostramos <b>novedades</b> del portal y avisos importantes."
                        });
                    }

                    steps.push({
                        intro: `
    <div>
      ¡Listo! Si necesitas apoyo adicional, visita el <b>Centro de ayuda</b> o usa el <b>Asistente virtual</b>.
      <div style="margin-top:.5rem;">
        <button onclick="startTourAyuda()" style="background:#7e22ce;color:#fff;border:none;border-radius:12px;padding:.4rem .8rem;font-weight:700;">
          Ver opciones de ayuda
        </button>
      </div>
    </div>
  `
                    });
                    return steps;
                }

                function startTour() {
                    const steps = buildSteps();
                    if (!window.introJs) {
                        alert('Intro.js no se cargó. Revisa tu conexión o la consola.');
                        return;
                    }
                    introJs().setOptions({
                            steps,
                            nextLabel: 'Siguiente →',
                            prevLabel: '← Atrás',
                            doneLabel: 'Entendido',
                            skipLabel: 'Saltar',
                            showProgress: true,
                            scrollToElement: true,
                            disableInteraction: true,
                            exitOnOverlayClick: true,
                        })
                        .oncomplete(() => localStorage.setItem(KEY, '1'))
                        .onexit(() => localStorage.setItem(KEY, '1'))
                        .start();
                }

                document.addEventListener('DOMContentLoaded', function() {
                    const btn = document.getElementById('btnTourHome');
                    if (btn) {
                        btn.addEventListener('click', function() {
                            console.log('[tour] click botón');
                            startTour();
                        });
                    }

                    // Auto-inicio solo la primera vez
                    if (!localStorage.getItem(KEY)) {
                        setTimeout(startTour, 700);
                    }
                });
            })();
        </script>
        <script>
            function startTourAyuda() {
                if (!window.introJs) return;

                if (window.__activeTour && typeof window.__activeTour.exit === 'function') {
                    try {
                        window.__activeTour.exit();
                    } catch (e) {}
                    window.__activeTour = null;
                }
                const steps = [];

                if (document.querySelector('#btnAyuda')) {
                    steps.push({
                        element: '#btnAyuda',
                        position: 'bottom',
                        intro: `
        <div><b>Centro de ayuda</b><br>
        Encuentra respuestas rápidas y guías paso a paso.</div>
      `
                    });
                }
                if (document.querySelector('#btnAsistenteVirtual')) {
                    steps.push({
                        element: '#btnAsistenteVirtual',
                        position: 'bottom',
                        intro: `
        <div><b>Asistente virtual</b><br>
        Haz preguntas y recibe ayuda inmediata.</div>
      `
                    });
                }
                if (document.querySelector('#btnCalificar')) {
                    steps.push({
                        element: '#btnCalificar',
                        position: 'bottom',
                        intro: `
        <div><b>Calificar la app</b><br>
        Dinos qué te pareció para seguir mejorando.</div>
      `
                    });
                }

                introJs().setOptions({
                    steps,
                    nextLabel: 'Siguiente →',
                    prevLabel: '← Atrás',
                    doneLabel: 'Entendido',
                    skipLabel: 'Saltar',
                    showProgress: true,
                    scrollToElement: true,
                    exitOnOverlayClick: true,
                }).start();
            }
        </script>

        <style>
            /* 💜 Barra de progreso morada */
            .introjs-progressbar {
                background-color: #7e22ce !important;
                /* purple-700 */
            }

            /* 🔘 Texto "Saltar" más pequeño y color gris claro */
            .introjs-skipbutton {
                font-size: 0.8rem !important;
                color: #737374 !important;
                /* gray-100 */
                opacity: 0.9;
            }

            .introjs-skipbutton:hover {
                color: #393838 !important;
                opacity: 1;
            }

            .introjs-helperLayer {
                box-shadow: 0 0 0 9999px rgba(17, 24, 39, 0.45),
                    0 0 0 4px rgba(168, 85, 247, .75) !important;
                border-radius: 1rem !important;
            }

            .introjs-tooltip {
                border-radius: 0.75rem !important;
            }

            .introjs-tooltiptext {
                font-size: 0.95rem !important;
                line-height: 1.35rem;
            }

            .introjs-nextbutton,
            .introjs-prevbutton,
            .introjs-donebutton {
                border-radius: .7rem !important;
                font-weight: 700 !important;
            }
        </style>
    @endsection
