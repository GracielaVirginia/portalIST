{{-- edit --}}
@extends('layouts.app')
@section('title','Editar FAQ')
@section('content')
<div class="px-6 py-6 max-w-3xl mx-auto bg-white dark:bg-gray-900 rounded-2xl shadow p-4">
  <h1 class="text-xl font-bold mb-4">Editar FAQ</h1>
  <form method="POST" action="{{ route('admin.faqs.update', $faq) }}">
    @method('PUT')
    @include('admin.faqs._form', ['faq'=>$faq])
  </form>
</div>
@endsection
