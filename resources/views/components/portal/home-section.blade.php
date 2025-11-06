@php
  use Illuminate\Support\Facades\DB;

  $get = fn($k,$d=null) => optional(DB::table('system_settings')->where('clave',$k)->first())->valor ?? $d;

  $type   = $get('home_section_tipo', 'cards');
  $titulo = $get('home_banner_titulo', '¡Conoce las nuevas funcionalidades del Portal Salud IST!');
  $texto  = $get('home_banner_texto', 'Accede desde tu celular y gestiona tus atenciones médicas fácilmente.');
  $cta    = $get('home_banner_cta', 'Conoce más →');
  $url    = $get('home_banner_url', '/promociones');

  $cards = json_decode($get('home_cards', '[]'), true) ?: [
    ['icon'=>'💬','titulo'=>'Atención personalizada','texto'=>'Agenda tus consultas de forma rápida y segura.'],
    ['icon'=>'🩺','titulo'=>'Salud preventiva','texto'=>'Programas y controles para tu bienestar.'],
    ['icon'=>'📱','titulo'=>'Resultados en línea','texto'=>'Consulta informes y exámenes cuando quieras.'],
  ];
@endphp

@if ($type === 'banner')
  <section
    class="mt-12 relative py-10 rounded-3xl shadow-xl text-center text-white overflow-hidden
           bg-gradient-to-r from-purple-900 via-purple-800 to-purple-700
           dark:from-gray-900 dark:via-gray-800 dark:to-gray-700">

    {{-- Textura translúcida (se mantiene sutil en ambos modos) --}}
    <div
      class="absolute inset-0 opacity-10 dark:opacity-15
             bg-[url('data:image/svg+xml;utf8,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%22200%22 height=%22200%22 fill=%22none%22 stroke=%22white%22 stroke-opacity=%220.15%22><circle cx=%22100%22 cy=%22100%22 r=%2290%22 stroke-width=%223%22/><path d=%22M100 10v180M10 100h180%22 stroke-width=%222%22/></svg>')]
             bg-[length:180px_180px]">
    </div>

    {{-- Contenido --}}
    <div class="relative z-10">
      <h3 class="text-2xl font-bold">{{ $titulo }}</h3>
      <p class="mt-2 text-sm text-purple-100 dark:text-gray-200">{{ $texto }}</p>

      <a href="{{ $url }}"
         class="inline-block mt-5 px-6 py-3 rounded-full font-semibold shadow transition
                bg-white text-purple-900 hover:bg-purple-50
                dark:bg-gray-100 dark:text-gray-900 dark:hover:bg-gray-200">
        {{ $cta }}
      </a>
    </div>
  </section>
@else
  <section
    class="mt-12 py-10 rounded-3xl border
           bg-purple-50/50 border-purple-200/40
           dark:bg-gray-900/40 dark:border-gray-700/40">
    <div class="max-w-6xl mx-auto grid grid-cols-1 sm:grid-cols-3 gap-6 px-6">
      @foreach($cards as $c)
        <div class="p-6 rounded-2xl bg-white shadow-md border text-center
                    border-purple-200/40
                    dark:bg-gray-800 dark:border-gray-700">
          <div class="text-4xl mb-2">{{ $c['icon'] ?? '✨' }}</div>
          <h4 class="font-semibold text-purple-900 dark:text-gray-100">{{ $c['titulo'] ?? '' }}</h4>
          <p class="text-sm text-gray-600 dark:text-gray-300 mt-2">{{ $c['texto'] ?? '' }}</p>
        </div>
      @endforeach
    </div>
  </section>
@endif

