@extends('layouts.app')
@section('title','Asistente Virtual — Editar Regla')
@section('content')
<div class="px-6 py-6 max-w-3xl mx-auto bg-white dark:bg-gray-900 rounded-2xl shadow p-4">
  <h2 class="text-2xl font-bold text-purple-900 dark:text-purple-100 mb-6">Asistente Virtual — Editar Regla</h2>
  <form method="POST" action="{{ route('admin.assistant_rules.update', $rule) }}">
    @csrf @method('PUT')
    @include('admin.assistant_rules._form', ['rule' => $rule])
  </form>
</div>
@endsection
