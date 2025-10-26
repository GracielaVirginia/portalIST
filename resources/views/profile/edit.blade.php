{{-- resources/views/profile/edit.blade.php --}}
@extends('layouts.app', ['topbar' => true, 'navbar' => true])

@section('title', 'Editar perfil')

@section('content')
<div class="max-w-2xl mx-auto px-6 py-6">
  <h1 class="text-2xl font-bold text-purple-700 dark:text-purple-200 mb-4">👤 Editar perfil</h1>

  {{-- Mensajes flash (si los tienes en el layout, puedes quitar este bloque) --}}
  @if (session('success'))
    <div class="mb-4 rounded-xl border border-green-300 dark:border-green-700 bg-green-50 dark:bg-green-900/30 text-green-800 dark:text-green-200 px-4 py-3 font-semibold">
      ✅ {{ session('success') }}
    </div>
  @endif

  {{-- Form update perfil (name/email). Ajusta los campos a tu User --}}
  <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
    @csrf
    @method('PATCH')

    <div>
      <label class="block text-sm font-semibold mb-1">Nombre</label>
      <input name="name" value="{{ old('name', Auth::user()->name) }}" class="w-full rounded-xl border p-3" required>
      @error('name') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
      <label class="block text-sm font-semibold mb-1">Email</label>
      <input type="email" name="email" value="{{ old('email', Auth::user()->email) }}" class="w-full rounded-xl border p-3" required>
      @error('email') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="flex items-center gap-3">
      <button class="rounded-xl bg-purple-900 text-white px-4 py-2 font-semibold hover:bg-purple-800">
        💾 Guardar
      </button>
      <a href="{{ url()->previous() }}" class="rounded-xl border px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-800">
        Cancelar
      </a>
    </div>
  </form>

  {{-- (Opcional) enlace para cambiar contraseña si tienes esa ruta --}}
  @if (Route::has('password.change'))
    <div class="mt-6">
      <a href="{{ route('password.change') }}" class="text-sm text-purple-700 dark:text-purple-300 underline">Cambiar contraseña</a>
    </div>
  @endif>
</div>
@endsection
