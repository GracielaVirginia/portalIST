@extends('layouts.admin')

@section('title', 'Editar Cita — Admin')

@section('admin')
<div class="px-6 py-6">
  <div class="flex items-center justify-between mb-4">
    <x-admin.nav-actions
      backHref="{{ route('admin.citas.index') }}"
      logoutRoute="admin.logout"
      variant="inline"
    />
    <h1 class="text-xl font-semibold">Editar cita #{{ $cita->id }}</h1>
  </div>

  <div class="bg-white dark:bg-gray-900 rounded-2xl shadow p-6">
    <form method="POST" action="{{ route('admin.citas.update', $cita) }}">
      @csrf
      @method('PUT')
      @include('admin.citas._form')
    </form>
  </div>
</div>
@endsection
