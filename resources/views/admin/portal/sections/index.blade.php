{{-- resources/views/admin/portal/sections/index.blade.php --}}
@php
  // Parámetros
  $page = $page ?? request('pagina','conoce-mas');
@endphp

@extends('layouts.app') {{-- ajusta a tu layout --}}

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">
  <div class="flex items-center justify-between mb-6">
    <div>
      <h1 class="text-2xl font-bold text-purple-900 dark:text-purple-100">Secciones: Conoce más</h1>
      <p class="text-sm text-gray-600 dark:text-gray-400">Administra los bloques de la página <span class="font-medium">"{{ $page }}"</span>.</p>
    </div>

    {{-- Crear nuevo --}}
    <form action="{{ route('admin.portal.sections.store') }}" method="POST" class="flex items-center gap-2">
      @csrf
      <input type="hidden" name="page_slug" value="{{ $page }}">

      <select name="tipo" class="rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-sm px-3 py-2">
        <option value="hero">Hero</option>
        <option value="beneficios">Beneficios</option>
        <option value="como_funciona">Cómo funciona</option>
        <option value="novedades">Novedades</option>
        <option value="testimonios">Testimonios</option>
        <option value="kpis">KPIs</option>
        <option value="seguridad">Seguridad</option>
      </select>

      <button type="submit"
        class="px-4 py-2 rounded-lg bg-purple-700 text-white text-sm font-semibold hover:bg-purple-600">
        + Crear bloque
      </button>
    </form>
  </div>

  {{-- flashes --}}
  @if(session('success'))
    <div class="mb-4 px-4 py-3 rounded-lg bg-green-100 text-green-900 border border-green-300">{{ session('success') }}</div>
  @endif
  @if($errors->any())
    <div class="mb-4 px-4 py-3 rounded-lg bg-red-100 text-red-900 border border-red-300">
      {{ $errors->first() }}
    </div>
  @endif

  {{-- Tabla drag & drop --}}
  <div class="rounded-xl border border-purple-200/50 dark:border-purple-800/40 overflow-hidden bg-white dark:bg-gray-900 shadow">
    <div class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300 border-b border-purple-200/40 dark:border-purple-800/40">
      Arrastra para reordenar. Cambios se guardan automáticamente.
    </div>

    <ul id="sortable"
        class="divide-y divide-purple-200/50 dark:divide-purple-800/40">
      @forelse($sections as $s)
        <li class="sortable-item flex items-center justify-between px-4 py-3 gap-4"
            data-id="{{ $s->id }}">
          <div class="flex items-center gap-3 min-w-0">
            <button type="button" class="handle cursor-grab text-gray-500 hover:text-gray-800 dark:hover:text-gray-200" title="Arrastrar">
              ⋮⋮
            </button>
            <div class="min-w-0">
              <div class="flex items-center gap-2">
                <span class="text-xs px-2 py-0.5 rounded-full bg-purple-100 dark:bg-purple-900/40 text-purple-900 dark:text-purple-200 border border-purple-200/40 dark:border-purple-800/40">
                  {{ $s->tipo }}
                </span>
                <span class="text-xs px-2 py-0.5 rounded-full {{ $s->visible ? 'bg-green-100 text-green-800 border-green-200' : 'bg-gray-100 text-gray-700 border-gray-200' }} border">
                  {{ $s->visible ? 'visible' : 'oculto' }}
                </span>
              </div>
              <div class="text-sm font-semibold truncate">
                {{ $s->titulo ?? '— sin título —' }}
              </div>
              <div class="text-xs text-gray-600 dark:text-gray-400 truncate max-w-xl">
                @if($s->publicar_desde || $s->publicar_hasta)
                  Publicación:
                  {{ $s->publicar_desde ? $s->publicar_desde->format('Y-m-d') : '—' }}
                  →
                  {{ $s->publicar_hasta ? $s->publicar_hasta->format('Y-m-d') : '—' }}
                @else
                  Sin ventana de publicación
                @endif
              </div>
            </div>
          </div>

          <div class="flex items-center gap-2">
            {{-- Toggle --}}
            <form action="{{ route('admin.portal.sections.toggle', $s) }}" method="POST" onsubmit="return confirm('¿Cambiar visibilidad?')">
              @csrf @method('PATCH')
              <button type="submit"
                class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ $s->visible ? 'bg-yellow-100 text-yellow-800 border border-yellow-300' : 'bg-green-100 text-green-800 border border-green-300' }}">
                {{ $s->visible ? 'Ocultar' : 'Mostrar' }}
              </button>
            </form>

            {{-- Editar --}}
            <a href="{{ route('admin.portal.sections.edit', $s) }}"
               class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-blue-600 text-white hover:bg-blue-500">
              Editar
            </a>

            {{-- Eliminar --}}
            <form action="{{ route('admin.portal.sections.destroy', $s) }}" method="POST"
                  onsubmit="return confirm('¿Eliminar bloque? Esta acción no se puede deshacer.')">
              @csrf @method('DELETE')
              <button type="submit"
                class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-red-600 text-white hover:bg-red-500">
                Eliminar
              </button>
            </form>
          </div>
        </li>
      @empty
        <li class="px-4 py-6 text-sm text-gray-600 dark:text-gray-400">
          Aún no hay bloques. Usa “+ Crear bloque” para empezar.
        </li>
      @endforelse
    </ul>
  </div>
</div>

{{-- Reordenamiento con drag & drop (vanilla) --}}
<script>
(function(){
  const list = document.getElementById('sortable');
  if (!list) return;

  let dragSrc;

  function handleDragStart(e) {
    dragSrc = this;
    this.style.opacity = '0.5';
    e.dataTransfer.effectAllowed = 'move';
    e.dataTransfer.setData('text/plain', this.dataset.id);
  }
  function handleDragOver(e) {
    e.preventDefault();
    e.dataTransfer.dropEffect = 'move';
    return false;
  }
  function handleDragEnter() { this.classList.add('bg-purple-50','dark:bg-gray-800/40'); }
  function handleDragLeave() { this.classList.remove('bg-purple-50','dark:bg-gray-800/40'); }
  function handleDrop(e) {
    e.stopPropagation();
    const id = e.dataTransfer.getData('text/plain');
    if (id && dragSrc !== this) {
      // insertar antes o después según posición
      const rect = this.getBoundingClientRect();
      const before = (e.clientY - rect.top) < rect.height / 2;
      list.insertBefore(dragSrc, before ? this : this.nextSibling);
      saveOrder();
    }
    return false;
  }
  function handleDragEnd() {
    this.style.opacity = '';
    list.querySelectorAll('.sortable-item').forEach(i => i.classList.remove('bg-purple-50','dark:bg-gray-800/40'));
  }

  list.querySelectorAll('.sortable-item').forEach(item => {
    item.draggable = true;
    item.addEventListener('dragstart', handleDragStart);
    item.addEventListener('dragover', handleDragOver);
    item.addEventListener('dragenter', handleDragEnter);
    item.addEventListener('dragleave', handleDragLeave);
    item.addEventListener('drop', handleDrop);
    item.addEventListener('dragend', handleDragEnd);
    // Permite “agarrar” desde el ícono, pero no es obligatorio:
    item.querySelector('.handle')?.addEventListener('mousedown', (e) => item.draggable = true);
  });

  async function saveOrder() {
    const ids = Array.from(list.querySelectorAll('.sortable-item')).map(li => li.dataset.id);
    try {
      const res = await fetch(@json(route('admin.portal.sections.reorder')), {
        method: 'PATCH',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('input[name=_token]')?.value || '{{ csrf_token() }}'
        },
        body: JSON.stringify({ page_slug: @json($page), order: ids })
      });
      if (!res.ok) throw new Error('HTTP '+res.status);
      // opcional: toast
      console.log('Orden actualizado');
    } catch (e) {
      console.error(e);
      alert('No se pudo actualizar el orden.');
    }
  }
})();
</script>
@endsection
