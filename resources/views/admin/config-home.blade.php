@extends('layouts.admin')

@section('title', 'Configuración — Sección Inferior del Portal')

@section('admin')
<div class="max-w-5xl mx-auto px-6 py-8">
          <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
  <x-admin.nav-actions
    backHref="{{ route('admin.dashboard') }}"
    logoutRoute="admin.logout"
    variant="inline"   {{-- o "sticky" si la tabla es larga --}}
  />       
            </div>
        </div>

  <h1 class="text-2xl font-bold text-purple-900 dark:text-purple-100 mb-6">
    ⚙️ Configuración de la Sección Inferior del Portal
  </h1>



  <form method="POST" action="{{ route('admin.config.home.update') }}" class="space-y-8" >
    @csrf
{{-- GALERÍA EN CARRUSEL + PANEL LATERAL DE SUBIDA --}}
<div class="mt-8">
  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
    Imagen de fondo del banner:
  </label>

  {{-- Campo oculto con el nombre de la imagen seleccionada (se envía al submit) --}}
  <input type="hidden" name="home_banner_imagen" id="home_banner_imagen"
         value="{{ $banner['imagen'] ?? '' }}">

  <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-start">
    {{-- Carrusel --}}
    <div class="md:col-span-3">
      {{-- Vista previa --}}
      <div class="mb-4">
        <img id="previewImagenSeleccionada"
             src="{{ isset($banner['imagen']) ? asset('images/' . $banner['imagen']) : asset('images/placeholder.png') }}"
             class="w-full h-48 object-cover rounded-xl border border-purple-300/40 dark:border-purple-700/40 shadow-sm"
             alt="Imagen seleccionada">
      </div>

      <div class="relative">
        {{-- Botones Prev/Next --}}
        <button type="button" id="btnPrev"
                class="absolute left-2 top-1/2 -translate-y-1/2 z-10 bg-white/80 dark:bg-gray-800/80 border border-gray-300 dark:border-gray-700 rounded-full w-9 h-9 flex items-center justify-center hover:shadow transition"
                aria-label="Anterior">
          ‹
        </button>
        <button type="button" id="btnNext"
                class="absolute right-2 top-1/2 -translate-y-1/2 z-10 bg-white/80 dark:bg-gray-800/80 border border-gray-300 dark:border-gray-700 rounded-full w-9 h-9 flex items-center justify-center hover:shadow transition"
                aria-label="Siguiente">
          ›
        </button>

        {{-- Contenedor deslizante --}}
        <div id="carouselImagenes"
             class="overflow-x-auto whitespace-nowrap scroll-smooth snap-x snap-mandatory px-1 py-2 rounded-xl border border-purple-300/40 dark:border-purple-700/40 bg-purple-50/20 dark:bg-gray-800/40">
          @foreach($imagenes as $img)
            <div class="inline-block align-top mr-3 snap-start">
              <div class="relative group w-44 h-32 rounded-lg overflow-hidden border border-gray-300 dark:border-gray-700 bg-black/5">
                <img src="{{ asset('images/' . $img->nombre) }}"
                     alt="{{ $img->nombre }}"
                     class="w-44 h-32 object-cover pointer-events-none">
                {{-- Overlay con acciones --}}
                <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition flex flex-col items-center justify-center gap-2 bg-black/45">
                  <button type="button"
                          class="px-3 py-1.5 rounded-md bg-purple-600 text-white text-xs font-semibold hover:bg-purple-500"
                          onclick="seleccionarImagen('{{ $img->nombre }}', '{{ asset('images/' . $img->nombre) }}')">
                    Seleccionar
                  </button>

                  <button type="button"
                          class="px-3 py-1.5 rounded-md bg-red-600 text-white text-xs font-semibold hover:bg-red-500"
                          onclick="eliminarImagen({{ $img->id }}, '{{ $img->nombre }}', this)">
                    Eliminar
                  </button>
                </div>
              </div>
              <div class="mt-1 text-[11px] text-center text-gray-600 dark:text-gray-400 truncate w-44" title="{{ $img->nombre }}">
                {{ $img->nombre }}
              </div>
            </div>
          @endforeach
        </div>
      </div>
    </div>

    {{-- Panel lateral: Agregar nueva imagen --}}
    <div class="md:col-span-1">
      <div class="p-4 rounded-xl border border-purple-300/40 dark:border-purple-700/40 bg-purple-50/30 dark:bg-gray-800/40">
        <h3 class="font-semibold text-purple-900 dark:text-purple-100 mb-2">Agregar imagen</h3>
        <p class="text-xs text-gray-600 dark:text-gray-400 mb-3">
          JPG, PNG, WEBP o SVG. Máx 4&nbsp;MB.
        </p>

        <input id="inputNuevaImagen" type="file" accept=".jpg,.jpeg,.png,.webp,.svg" class="hidden">

        <button type="button"
                class="w-full px-3 py-2 rounded-lg bg-purple-700 text-white text-sm font-medium hover:bg-purple-600 shadow"
                onclick="document.getElementById('inputNuevaImagen').click()">
          + Subir imagen
        </button>

        <div id="uploadHint" class="text-[11px] text-gray-500 dark:text-gray-400 mt-2"></div>
      </div>
    </div>
  </div>
</div>

{{-- ====== Scripts de interacción ====== --}}

    {{-- SELECCIÓN TIPO --}}
    <div>
      <label class="block font-semibold text-purple-900 dark:text-purple-200 mb-2">
        Tipo de sección a mostrar:
      </label>
      <select name="home_section_tipo"
              id="home_section_tipo"
              class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-sm px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-purple-500">
        <option value="banner" {{ $tipo === 'banner' ? 'selected' : '' }}>Banner</option>
        <option value="cards" {{ $tipo === 'cards' ? 'selected' : '' }}>Tres Cards</option>
      </select>
      <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Selecciona qué tipo de bloque se mostrará en la parte inferior del portal.</p>
    </div>

    {{-- CONFIGURACIÓN BANNER --}}
    <div id="bannerConfig" class="{{ $tipo === 'banner' ? '' : 'hidden' }}">
      <div class="border-t border-purple-300/40 dark:border-purple-700/40 my-4"></div>
      <h2 class="text-lg font-bold text-purple-800 dark:text-purple-200 mb-3">🎨 Banner</h2>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Título</label>
          <input type="text" name="home_banner_titulo" value="{{ $banner['titulo'] ?? '' }}"
                 class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 text-sm px-3 py-2 focus:ring-2 focus:ring-purple-500">
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Texto</label>
          <input type="text" name="home_banner_texto" value="{{ $banner['texto'] ?? '' }}"
                 class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 text-sm px-3 py-2">
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Texto del botón</label>
          <input type="text" name="home_banner_cta" value="{{ $banner['cta'] ?? '' }}"
                 class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 text-sm px-3 py-2">
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Enlace del botón</label>
          <input type="text" name="home_banner_url" value="{{ $banner['url'] ?? '' }}"
                 class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 text-sm px-3 py-2">
        </div>
      </div>
    </div>

    {{-- CONFIGURACIÓN CARDS --}}
    <div id="cardsConfig" class="{{ $tipo === 'cards' ? '' : 'hidden' }}">
      <div class="border-t border-purple-300/40 dark:border-purple-700/40 my-4"></div>
      <h2 class="text-lg font-bold text-purple-800 dark:text-purple-200 mb-3">🟣 Tres Cards</h2>

      <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">Edita los textos o íconos que aparecerán en cada card.</p>

      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        @foreach($cards as $i => $c)
        <div class="p-4 rounded-xl border border-purple-200/50 dark:border-purple-700/50 bg-purple-50/30 dark:bg-gray-800/40">
          <h3 class="font-semibold text-purple-900 dark:text-purple-100 mb-2">Card {{ $i+1 }}</h3>
          <label class="block text-xs text-gray-600 dark:text-gray-400">Icono</label>
          <input type="text" name="cards[{{ $i }}][icon]" value="{{ $c['icon'] ?? '' }}"
                 class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 text-sm px-3 py-2 mb-2">

          <label class="block text-xs text-gray-600 dark:text-gray-400">Título</label>
          <input type="text" name="cards[{{ $i }}][titulo]" value="{{ $c['titulo'] ?? '' }}"
                 class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 text-sm px-3 py-2 mb-2">

          <label class="block text-xs text-gray-600 dark:text-gray-400">Texto</label>
          <textarea name="cards[{{ $i }}][texto]" rows="2"
                    class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 text-sm px-3 py-2">{{ $c['texto'] ?? '' }}</textarea>
        </div>
        @endforeach
      </div>
    </div>

    <div class="pt-6 border-t border-purple-300/40 dark:border-purple-700/40">
      <button type="submit"
              class="px-6 py-3 rounded-lg font-semibold bg-purple-900 text-white hover:bg-purple-800 shadow">
        💾 Guardar Cambios
      </button>
    </div>
  </form>
</div>

{{-- Script para alternar visible banner/cards --}}
<script>
document.getElementById('home_section_tipo').addEventListener('change', function() {
  const banner = document.getElementById('bannerConfig');
  const cards = document.getElementById('cardsConfig');
  if (this.value === 'banner') {
    banner.classList.remove('hidden');
    cards.classList.add('hidden');
  } else {
    cards.classList.remove('hidden');
    banner.classList.add('hidden');
  }
});
</script>
<script>
(function(){
  const carrusel = document.getElementById('carouselImagenes');
  const btnPrev  = document.getElementById('btnPrev');
  const btnNext  = document.getElementById('btnNext');
  const inputSel = document.getElementById('home_banner_imagen');
  const preview  = document.getElementById('previewImagenSeleccionada');
  const inputNueva = document.getElementById('inputNuevaImagen');
  const uploadHint = document.getElementById('uploadHint');

  // Utilidad: CSRF desde el form principal
  function getCsrf() {
    const i = document.querySelector('input[name="_token"]');
    return i ? i.value : '';
  }

  // Desplazamiento carrusel
  function scrollItemWidth() {
    const item = carrusel.querySelector('.inline-block');
    return item ? (item.getBoundingClientRect().width + 12) : 200; // 12 ~ mr-3
  }
  btnPrev?.addEventListener('click', () => carrusel.scrollBy({ left: -scrollItemWidth() * 2, behavior: 'smooth' }));
  btnNext?.addEventListener('click', () => carrusel.scrollBy({ left:  scrollItemWidth() * 2, behavior: 'smooth' }));

  // Seleccionar imagen
  window.seleccionarImagen = function(nombre, url) {
    if (inputSel) inputSel.value = nombre;
    if (preview) preview.src = url;
    // realce
    preview.classList.add('ring-4','ring-purple-500','transition');
    setTimeout(() => preview.classList.remove('ring-4','ring-purple-500'), 500);
  };

  // Agregar (subir) imagen vía fetch sin anidar forms
  inputNueva?.addEventListener('change', async function() {
    if (!this.files || !this.files[0]) return;
    const file = this.files[0];

    const formData = new FormData();
    formData.append('image', file);

    uploadHint.textContent = 'Subiendo…';
    try {
      const res = await fetch(@json(route('admin.images.store')), {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': getCsrf() },
        body: formData
      });
      if (!res.ok) throw new Error('HTTP ' + res.status);
      const json = await res.json();
      // json esperado: { id, nombre, url }
      agregarItemAlCarrusel(json.id, json.nombre, json.url);
      uploadHint.textContent = 'Imagen subida.';
      this.value = ''; // limpiar input
    } catch (e) {
      console.error(e);
      uploadHint.textContent = 'No se pudo subir la imagen.';
    } finally {
      setTimeout(() => uploadHint.textContent = '', 1800);
    }
  });

  // Eliminar imagen
  window.eliminarImagen = async function(id, nombre, btnEl) {
    if (!confirm('¿Eliminar esta imagen?')) return;

    try {
      const res = await fetch(@json(route('admin.images.destroy', 0)).replace('/0', '/' + id), {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8',
          'X-CSRF-TOKEN': getCsrf()
        },
        body: new URLSearchParams({ _method: 'DELETE' })
      });
      if (!res.ok) throw new Error('HTTP ' + res.status);

      // Quitar del DOM
      const card = btnEl.closest('.inline-block');
      if (card) card.remove();

      // Si estaba seleccionada, limpiar selección
      if (inputSel && inputSel.value === nombre) {
        inputSel.value = '';
        if (preview) {
          preview.src = @json(asset('images/placeholder.png'));
        }
      }
    } catch (e) {
      console.error(e);
      alert('No se pudo eliminar la imagen.');
    }
  };

  // Añadir nuevo item al carrusel (tras upload)
  function agregarItemAlCarrusel(id, nombre, url) {
    const wrap = document.createElement('div');
    wrap.className = 'inline-block align-top mr-3 snap-start';
    wrap.innerHTML = `
      <div class="relative group w-44 h-32 rounded-lg overflow-hidden border border-gray-300 dark:border-gray-700 bg-black/5">
        <img src="${url}" alt="${nombre}" class="w-44 h-32 object-cover pointer-events-none">
        <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition flex flex-col items-center justify-center gap-2 bg-black/45">
          <button type="button"
                  class="px-3 py-1.5 rounded-md bg-purple-600 text-white text-xs font-semibold hover:bg-purple-500"
                  onclick="seleccionarImagen('${nombre}', '${url}')">Seleccionar</button>
          <button type="button"
                  class="px-3 py-1.5 rounded-md bg-red-600 text-white text-xs font-semibold hover:bg-red-500"
                  onclick="eliminarImagen(${id}, '${nombre}', this)">Eliminar</button>
        </div>
      </div>
      <div class="mt-1 text-[11px] text-center text-gray-600 dark:text-gray-400 truncate w-44" title="${nombre}">
        ${nombre}
      </div>
    `;
    carrusel?.appendChild(wrap);
    // Desplazar hacia el final para mostrar la nueva
    carrusel?.scrollTo({ left: carrusel.scrollWidth, behavior: 'smooth' });
  }
})();
</script>

@endsection
