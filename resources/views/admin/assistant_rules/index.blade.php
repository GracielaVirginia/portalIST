@extends('layouts.app')
@section('title','Asistente Virtual — Reglas')
@section('content')
<div class="px-6 py-6">
  <div class="flex items-center justify-between mb-4">
    <div class="flex items-center gap-3">
      <h1 class="text-xl font-bold text-purple-900 dark:text-purple-100 mb-2">Asistente Virtual — Reglas</h1>
      {{-- Volver al dashboard --}}
  <x-admin.nav-actions
    backHref="{{ route('admin.dashboard') }}"
    logoutRoute="admin.logout"
    variant="inline"   {{-- o "sticky" si la tabla es larga --}}
  />
    </div>
      {{-- Tooltip "i" --}}
      <div class="relative group">
        <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-purple-100 text-purple-900 border border-purple-200 text-xs font-bold select-none cursor-default">i</span>
        <div class="absolute left-0 mt-2 w-[22rem] rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-200 text-sm p-3 shadow-xl opacity-0 scale-95 translate-y-1 group-hover:opacity-100 group-hover:scale-100 group-hover:translate-y-0 transition pointer-events-none group-hover:pointer-events-auto z-10">
          <p class="mb-1">Cada regla define palabras clave o patrones regex y la respuesta que verá el usuario.</p>
          <ul class="list-disc ml-5 space-y-1">
            <li><strong>ANY</strong>: responde si encuentra cualquiera de las claves.</li>
            <li><strong>ALL</strong>: requiere que estén todas las claves.</li>
            <li>Usa <em>Prioridad</em> para decidir qué regla se evalúa primero.</li>
          </ul>
        </div>
      </div>
    </div>
 <div class="flex justify-end mb-3">
    <a href="{{ route('admin.assistant_rules.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-purple-900 text-white text-sm font-semibold px-4 py-2 shadow hover:shadow-md">➕ Nueva</a>
  </div>

  <div class="bg-white dark:bg-gray-900 rounded-2xl shadow p-4">
    <table id="tablaRules" class="display w-full">
      <thead>
        <tr>
          <th>Título</th>
          <th>Activa</th>
          <th class="text-right">Acciones</th>
        </tr>
      </thead>
      <tbody>
        @foreach($rules as $r)
          <tr>
            <td class="font-semibold text-purple-900 dark:text-purple-200">{{ $r->title }}</td>
            <td>@if($r->is_active)<span class="inline-flex items-center gap-1 text-green-700 bg-green-100 px-2 py-1 rounded text-xs font-bold">Sí</span>@else <span class="text-xs text-gray-500">No</span>@endif</td>
            <td class="text-right">
              <div class="inline-flex items-center gap-2">
                <a href="{{ route('admin.assistant_rules.edit', $r) }}" class="btn-action">✏️ Editar</a>
                <form method="POST" action="{{ route('admin.assistant_rules.toggle', $r) }}" class="inline">@csrf @method('PATCH') <button type="submit" class="btn-action">↔️ Toggle</button></form>
                <form method="POST" action="{{ route('admin.assistant_rules.destroy', $r) }}" class="inline form-del">@csrf @method('DELETE') <button type="submit" class="btn-action">🗑️ Eliminar</button></form>
              </div>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
  if (window.DataTable) {
    new DataTable('#tablaRules', {
      pageLength: 10, order: [[3,'asc']], language:{ url:'//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' }
    });
  }
  document.querySelectorAll('.form-del').forEach(f=>{
    f.addEventListener('submit', e=>{
      e.preventDefault();
      Swal.fire({title:'¿Eliminar regla?', text:'Esta acción no se puede deshacer.', icon:'warning', showCancelButton:true, confirmButtonColor:'#7e22ce', cancelButtonText:'Cancelar', confirmButtonText:'Eliminar'}).then(r=>{ if(r.isConfirmed) f.submit(); });
    });
  });
});
</script>
@endpush
@endsection
