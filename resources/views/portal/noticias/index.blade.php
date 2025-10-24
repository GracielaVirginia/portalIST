@extends('layouts.app', ['topbar' => true, 'navbar' => true])

@section('title', 'Noticias')

@section('content')
  <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <h1 class="text-2xl font-bold text-purple-900 dark:text-gray-100 mb-6">
      Noticias y Consejos de Salud
    </h1>

    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
      @forelse ($noticias as $noticia)
        <x-portal.widget-noticia :noticia="$noticia" />
      @empty
        <div class="col-span-full text-center text-gray-500 dark:text-gray-400">
          No hay noticias disponibles por el momento.
        </div>
      @endforelse
    </div>
  </div>
@endsection
