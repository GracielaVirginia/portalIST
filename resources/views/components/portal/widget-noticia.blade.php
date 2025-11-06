{{-- resources/views/components/portal/widget-noticia.blade.php --}}
@props([
  'noticia' => [
    'id'     => null,
    'titulo' => 'Chequeo general: cuida tu salud preventiva',
    'bajada' => 'Recuerda registrar tus controles de tensión y glucosa. Si tienes lecturas altas por 3 días seguidos, agenda un control.',
    'imagen' => 'https://images.unsplash.com/photo-1585421514738-01798e348b17?q=80&w=1200&auto=format&fit=crop',
    'url'    => null, // si no viene, calculamos con el id
  ],
])

@php
  $t   = $noticia['titulo'] ?? '';
  $b   = $noticia['bajada'] ?? '';
  $img = $noticia['imagen'] ?? null;

  // Si no te pasan 'url', intenta construirla con el id.
  $u   = $noticia['url']
        ?? (isset($noticia['id']) ? route('portal.noticias.show', $noticia['id']) : '#');
@endphp

<article class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 overflow-hidden shadow-sm">
  @if($img)
    <div class="aspect-[14/4] w-full overflow-hidden">
      <img src="{{ $img }}" alt="Noticia destacada" class="h-full w-full object-cover">
    </div>
  @endif

  <div class="p-4 sm:p-5">
    <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-gray-100">
      {{ $t }}
    </h3>
    <p class="mt-2 text-sm text-gray-600 dark:text-gray-300 leading-relaxed">
      {{ $b }}
    </p>

    <div class="mt-4">
      <a href="{{ $u }}"
         class="group inline-flex items-center gap-2 rounded-xl border border-purple-900/20 dark:border-purple-300/20
                bg-purple-900 text-white px-4 py-2 text-sm font-semibold transition
                hover:bg-purple-700 hover:scale-[1.02] hover:shadow-md focus:ring-2 focus:ring-purple-400 focus:outline-none">
        <span>Leer más</span>
        <svg xmlns="http://www.w3.org/2000/svg"
             class="h-4 w-4 transition-transform group-hover:translate-x-1"
             viewBox="0 0 24 24" fill="currentColor">
          <path d="M12 4l1.41 1.41L8.83 10H20v2H8.83l4.58 4.59L12 18l-8-8 8-8z"/>
        </svg>
      </a>
    </div>
  </div>
</article>
