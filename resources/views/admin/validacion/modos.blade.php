@extends('layouts.admin')

@section('title', 'Formas de validar al usuario del portal')

@section('admin')
<div class="px-6 py-6 max-w-5xl mx-auto">
            <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
  <x-admin.nav-actions
    backHref="{{ route('admin.dashboard') }}"
    logoutRoute="admin.logout"
    variant="inline"   {{-- o "sticky" si la tabla es larga --}}
  />

            </div>
        </div>
  <h1 class="text-2xl font-bold text-purple-700 dark:text-purple-200">
    Formas de validar al usuario del portal
  </h1>

  <div class="mt-3 border-t border-purple-200/60 dark:border-purple-800/60"></div>

  @if (session('ok'))
    <div class="mt-4 rounded-lg bg-green-50 text-green-800 dark:bg-green-900/20 dark:text-green-200 px-4 py-3">
      {{ session('ok') }}
    </div>
  @endif

  <form method="POST" action="{{ route('admin.validacion.modos.guardar') }}" class="mt-6 space-y-6">
    @csrf

    @foreach ($opciones as $opt)
      @php
        $imgSrc = asset($opt->imagen ?? 'images/validaciones/placeholder.png');
        $imgAlt = $opt->nombre;
      @endphp

      <section class="pt-4">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center">
          {{-- Imagen (click abre modal) --}}
          <div class="md:col-span-3">
            <button type="button"
                    class="group block w-full text-left"
                    data-img-src="{{ $imgSrc }}"
                    data-img-alt="{{ $imgAlt }}"
                    onclick="window.__openImgModal(this)"
                    aria-label="Ver imagen ampliada de {{ $imgAlt }}">
              <div class="overflow-hidden rounded-xl ring-1 ring-purple-200/60 dark:ring-purple-800/60">
                <img
                  src="{{ $imgSrc }}"
                  alt="{{ $imgAlt }}"
                  class="w-full h-36 object-cover transition-transform duration-200 group-hover:scale-[1.03]"
                  onerror="this.src='{{ asset('images/validaciones/placeholder.png') }}'">
              </div>
              <span class="mt-2 inline-block text-xs text-gray-500 dark:text-gray-400">Click para ampliar</span>
            </button>
          </div>

          {{-- Texto --}}
          <div class="md:col-span-7">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
              {{ $opt->id }}. {{ $opt->nombre }}
            </h2>
            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">
              {{ $opt->descripcion }}
            </p>
          </div>

          {{-- Toggle --}}
          <div class="md:col-span-2 flex md:justify-end">
            <label class="inline-flex items-center gap-3 cursor-pointer select-none">
              <input
                type="radio"
                name="id"
                value="{{ $opt->id }}"
                class="peer sr-only"
                @checked($opt->activo)>
              <span class="block w-12 h-7 rounded-full bg-gray-300 dark:bg-gray-700 relative transition-all peer-checked:bg-purple-600">
                <span class="absolute top-1 left-1 w-5 h-5 rounded-full bg-white dark:bg-gray-200 shadow transition-all peer-checked:left-6"></span>
              </span>
              <span class="text-sm text-gray-700 dark:text-gray-200">Seleccionar</span>
            </label>
          </div>
        </div>

        @if(!$loop->last)
          <div class="mt-4 border-t border-purple-200/40 dark:border-purple-800/50"></div>
        @endif
      </section>
    @endforeach

    <div class="pt-2">
      <button
        type="submit"
        class="inline-flex items-center gap-2 rounded-xl bg-purple-900 text-white font-semibold px-5 py-2.5 shadow hover:bg-purple-800 hover:shadow-md transition">
        Guardar selección
      </button>
    </div>
  </form>
</div>

{{-- Modal de imagen (único, reutilizable) --}}
<div id="imgModal"
     class="hidden fixed inset-0 z-50 items-center justify-center bg-black/70 p-4"
     role="dialog" aria-modal="true" aria-labelledby="imgModalTitle">
  <div class="relative max-w-5xl w-full">
    <button type="button"
            class="absolute -top-3 -right-3 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 rounded-full w-9 h-9 shadow flex items-center justify-center hover:scale-105 transition"
            onclick="window.__closeImgModal()" aria-label="Cerrar">
      ✕
    </button>

    <div class="bg-white dark:bg-gray-900 rounded-2xl overflow-hidden shadow-2xl">
      <div class="p-3 border-b border-gray-200 dark:border-gray-700">
        <h3 id="imgModalTitle" class="text-sm font-semibold text-gray-700 dark:text-gray-200">Vista previa</h3>
      </div>
      <div class="bg-black/80 flex items-center justify-center">
        <img id="imgModalImg"
             src=""
             alt=""
             class="max-h-[80vh] w-auto object-contain select-none"
             onerror="this.src='{{ asset('images/validaciones/placeholder.png') }}'">
      </div>
      <div class="p-3 text-right border-t border-gray-200 dark:border-gray-700">
        <button type="button"
                class="inline-flex items-center gap-2 rounded-lg bg-purple-900 text-white font-semibold px-4 py-2 hover:bg-purple-800 transition"
                onclick="window.__closeImgModal()">
          Cerrar
        </button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  (function () {
    const modal = document.getElementById('imgModal');
    const modalImg = document.getElementById('imgModalImg');
    const title = document.getElementById('imgModalTitle');

    function open(el) {
      const src = el.getAttribute('data-img-src') || '';
      const alt = el.getAttribute('data-img-alt') || 'Vista previa';
      modalImg.src = src;
      modalImg.alt = alt;
      title.textContent = alt || 'Vista previa';
      modal.classList.remove('hidden');
      modal.classList.add('flex');
      // bloquear scroll del body
      document.documentElement.style.overflow = 'hidden';
    }

    function close() {
      modal.classList.add('hidden');
      modal.classList.remove('flex');
      modalImg.src = '';
      document.documentElement.style.overflow = '';
    }

    // Exponer funciones para onclick en los botones
    window.__openImgModal = open;
    window.__closeImgModal = close;

    // Cerrar al hacer click en overlay
    modal.addEventListener('click', function (e) {
      if (e.target === modal) close();
    });

    // Cerrar con ESC
    document.addEventListener('keydown', function (e) {
      if (!modal.classList.contains('hidden') && e.key === 'Escape') {
        close();
      }
    });
  })();
</script>
@endpush
