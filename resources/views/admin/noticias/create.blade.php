@extends('layouts.app', ['topbar' => true, 'navbar' => true])
@section('title','Agregar noticia')

@section('content')
<div class="max-w-3xl mx-auto px-6 py-6">
  <h1 class="text-2xl font-bold text-purple-700 dark:text-purple-200 mb-4">Nueva noticia</h1>

  <form method="POST" action="{{ route('admin.noticias.store') }}" enctype="multipart/form-data" class="space-y-6">
    @include('admin.noticias._form')
  </form>
</div>
@endsection
