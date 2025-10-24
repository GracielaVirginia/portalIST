@extends('layouts.app')

@section('title', 'Admin — Dashboard')

@section('content')
    <x-admin.layout title="Dashboard">
        {{-- 🟣 Topbar --}}
        <x-slot:topbar>
            <x-admin.topbar :user-name="$admin->nombre_completo ?? 'Administrador'" search-url="{{ route('admin.users.search') }}"
                placeholder="Buscar paciente por RUT o nombre…" />
        </x-slot:topbar>

        {{-- 🟣 Sidebar --}}
        <x-slot:sidebar>
            <x-admin.sidebar :stats="$sidebarStats" />
        </x-slot:sidebar>

        {{-- Contenido principal --}}
        <div class="xl:col-span-2 bg-white/10 rounded-2xl p-4 mb-4 shadow">
        </div>
        <div class="bg-white/10 rounded-2xl p-4 shadow">
            <x-calendar id="calResultados" value="{{ now()->toDateString() }}" firstDay="1" />
                            @include('admin.partials.stats-panel')
@include('admin.partials.stats-chart') 
        </div>
    </x-admin.layout>
@endsection
