@extends('layouts.admin')

@section('title', 'Admin — Dashboard')

@section('admin')
    <x-admin.layout title="Dashboard">
        {{-- 🟣 Topbar --}}
<x-slot:topbar>
  <x-admin.topbar
      :user-name="$adminName"
      :alert-stats="$alertStats"
      audit-url="{{ route('admin.auth_attempts.index') }}"
      search-url="{{ route('admin.users.search') }}"
      placeholder="Buscar paciente por RUT o nombre…"
  />
</x-slot:topbar>

        {{-- 🟣 Sidebar --}}
        <x-slot:sidebar>
            <x-admin.sidebar :stats="$sidebarStats" />
        </x-slot:sidebar>

        {{-- Contenido principal --}}
        <div class="xl:col-span-2 bg-white/10 rounded-2xl p-4 mb-4 shadow">
            @include('admin.partials.stats-chart')

        </div>
        <div class="bg-white/10 rounded-2xl p-4 shadow">
            <x-calendar id="calResultados" value="{{ now()->toDateString() }}" firstDay="1" />
            @include('admin.partials.stats-panel')
        </div>
                <div class="xl:col-span-2 bg-white/10 rounded-2xl p-4 mb-4 shadow">
        @include('admin.partials.patient-detail')
                </div>
    </x-admin.layout>
@endsection
