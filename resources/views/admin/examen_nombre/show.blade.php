@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Detalle del Examen</h1>

        <div class="card">
            <div class="card-body">
                <h5 class="card-title">{{ $examenNombre->nombre }}</h5>
                <p class="card-text"><strong>Código:</strong> {{ $examenNombre->codigo }}</p>
                <p class="card-text"><strong>Tipo:</strong> {{ $examenNombre->tipo }}</p>
                <p class="card-text"><strong>Especialidad:</strong> {{ $examenNombre->especialidad->name }}</p>
                <a href="{{ route('examen_nombres.index') }}" class="btn btn-primary">Volver</a>
            </div>
        </div>
    </div>
@endsection
