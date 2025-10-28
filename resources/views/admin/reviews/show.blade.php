{{-- resources/views/admin/reviews/show.blade.php --}}
@extends('layouts.app', ['topbar' => true, 'navbar' => true])

@section('title', 'Detalle de Opinión')

@section('content')
  <div class="px-6 py-6">
    {{-- Botones superiores (mismo estilo que Tickets/Noticias) --}}
    <div class="flex items-center justify-between mb-4">
      <div class="flex items-center gap-3">
        <a href="{{ route('admin.reviews.index') }}"
           class="inline-flex items-center gap-2 bg-purple-900 text-white text-sm font-semibold px-4 py-2 rounded-full shadow-sm hover:bg-purple-800 hover:shadow-md transition">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
               stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
          </svg>
          Volver al listado
        </a>

        <a href="{{ route('admin.dashboard') }}"
           class="inline-flex items-center gap-2 bg-gray-200 dark:bg-gray-800 text-gray-800 dark:text-gray-200 text-sm font-semibold px-4 py-2 rounded-full shadow-sm hover:bg-gray-300 dark:hover:bg-gray-700 transition">
          🏠 Dashboard
        </a>
      </div>

      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit"
                class="inline-flex items-center gap-2 bg-red-600 text-white text-sm font-semibold px-4 py-2 rounded-full shadow-sm hover:bg-red-500 hover:shadow-md transition">
          🔒 Cerrar sesión
        </button>
      </form>
    </div>

    {{-- Card detalle --}}
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow p-6 border border-purple-100 dark:border-gray-700">
      <h1 class="text-2xl font-bold text-purple-800 dark:text-purple-200 mb-6">
        Opinión #{{ $review->id }}
      </h1>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Usuario --}}
        <div class="space-y-1">
          <div class="text-sm font-semibold text-gray-500 dark:text-gray-400">Usuario</div>
          <div class="text-gray-900 dark:text-gray-100">
            @if($review->anonimo || !$review->user)
              🕶️ Anónimo
            @else
              <div class="flex flex-col">
                <span class="font-semibold">{{ $review->user->name }}</span>
                <span class="text-sm text-gray-500">{{ $review->user->email }}</span>
              </div>
            @endif
          </div>
        </div>

        {{-- Fecha --}}
        <div class="space-y-1">
          <div class="text-sm font-semibold text-gray-500 dark:text-gray-400">Fecha</div>
          <div class="text-gray-900 dark:text-gray-100">
            {{ $review->created_at?->format('d-m-Y H:i') }}
          </div>
        </div>

        {{-- Rating --}}
        <div class="space-y-1">
          <div class="text-sm font-semibold text-gray-500 dark:text-gray-400">Calificación</div>
          <div class="text-yellow-500">
            @for($i=1; $i<=5; $i++)
              <span class="{{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}">★</span>
            @endfor
            <span class="ml-2 text-sm text-gray-600 dark:text-gray-300">({{ $review->rating }}/5)</span>
          </div>
        </div>

        {{-- Anónimo --}}
        <div class="space-y-1">
          <div class="text-sm font-semibold text-gray-500 dark:text-gray-400">¿Anónimo?</div>
          <div class="text-gray-900 dark:text-gray-100">
            {{ $review->anonimo ? 'Sí' : 'No' }}
          </div>
        </div>

        {{-- Comentario (columna completa) --}}
        <div class="md:col-span-2 space-y-2">
          <div class="text-sm font-semibold text-gray-500 dark:text-gray-400">Comentario</div>
          <div class="rounded-xl border border-purple-100 dark:border-gray-700 bg-purple-50/40 dark:bg-gray-800 p-4 text-gray-800 dark:text-gray-200">
            {!! nl2br(e($review->comment ?: '—')) !!}
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
