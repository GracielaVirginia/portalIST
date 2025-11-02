@extends('layouts.app')

@section('content')
@include('admin.promociones._form', [
    'title' => 'Nueva Promoción',
    'route' => route('admin.promociones.store'),
    'method' => 'POST',
])
@endsection
