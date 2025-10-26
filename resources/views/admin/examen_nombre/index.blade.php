@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Lista de Exámenes</h1>
        <a href="{{ route('examen_nombres.create') }}" class="btn btn-primary mb-3">Crear Nuevo Examen</a>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Tipo</th>
                    <th>Especialidad</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($examenes as $examen)
                    <tr>
                        <td>{{ $examen->codigo }}</td>
                        <td>{{ $examen->nombre }}</td>
                        <td>{{ $examen->tipo }}</td>
                        <td>{{ $examen->especialidad->name }}</td>
                        <td>
                            <a href="{{ route('examen_nombres.edit', $examen) }}" class="btn btn-warning">Editar</a>
                            <form action="{{ route('examen_nombres.destroy', $examen) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" onclick="return confirm('¿Estás seguro?')">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
