@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Editar Examen</h1>

        <form method="POST" action="{{ route('examen_nombres.update', $examenNombre) }}">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label>Código:</label>
                <input type="text" name="codigo" class="form-control" value="{{ $examenNombre->codigo }}" required>
            </div>
            <div class="form-group">
                <label>Nombre:</label>
                <input type="text" name="nombre" class="form-control" value="{{ $examenNombre->nombre }}" required>
            </div>
            <div class="form-group">
                <label>Tipo:</label>
                <input type="text" name="tipo" class="form-control" value="{{ $examenNombre->tipo }}" required>
            </div>
            <div class="form-group">
                <label>Especialidad:</label>
                <select name="especialidad_id" class="form-control" required>
                    @foreach($especialidades as $especialidad)
                        <option value="{{ $especialidad->id }}" {{ $examenNombre->especialidad_id == $especialidad->id ? 'selected' : '' }}>
                            {{ $especialidad->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Actualizar</button>
        </form>
    </div>
@endsection
