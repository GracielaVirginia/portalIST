@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-purple-100 px-4 py-8">
  <div class="mx-auto w-full max-w-5xl space-y-5">
          <x-ui.back-button :href="route('portal.home')" label="Volver" variant="outline" size="sm" class="mr-4" />

    {{-- Título --}}
    <div class="flex items-center justify-between">
      <h1 class="text-xl font-semibold text-gray-900">Mi historial médico</h1>
    </div>

    {{-- Componente de subida (botón morado que abre modal) --}}
    <div>
      <x-portal.upload-document />
    </div>

    {{-- Filtros: texto LIKE + rango de fechas --}}
    <form method="GET" action="{{ route('portal.historial.index') }}" class="rounded-2xl bg-white ring-1 ring-purple-200 p-3 sm:p-4 shadow-sm">
      <div class="grid grid-cols-1 sm:grid-cols-12 gap-3">
        <div class="sm:col-span-6">
          <label class="block text-xs text-gray-600 mb-1">Buscar en mis documentos</label>
          <input type="search" name="q" value="{{ request('q') }}"
                 class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-800 shadow-sm 
                        focus:border-purple-500 focus:ring-2 focus:ring-purple-200 focus:outline-none transition"
                 placeholder="Nombre, etiqueta o descripción…">
        </div>
        <div class="sm:col-span-3">
          <label class="block text-xs text-gray-600 mb-1">Desde</label>
          <input type="date" name="from" value="{{ request('from') }}"
                 class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-800 shadow-sm 
                        focus:border-purple-500 focus:ring-2 focus:ring-purple-200 focus:outline-none transition">
        </div>
        <div class="sm:col-span-3">
          <label class="block text-xs text-gray-600 mb-1">Hasta</label>
          <input type="date" name="to" value="{{ request('to') }}"
                 class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-800 shadow-sm 
                        focus:border-purple-500 focus:ring-2 focus:ring-purple-200 focus:outline-none transition">
        </div>
      </div>

      <div class="mt-3 flex items-center gap-2">
        <button class="rounded-full bg-purple-700 px-4 py-2 text-sm font-medium text-white hover:bg-purple-800 focus:outline-none focus:ring-2 focus:ring-purple-300">
          Filtrar
        </button>
        <a href="{{ route('portal.historial.index') }}" class="text-sm text-gray-600 hover:underline">Limpiar</a>
      </div>
    </form>

    {{-- Lista de documentos --}}
    <div class="space-y-2">
      @forelse ($docs as $doc)
        <div class="flex items-center justify-between rounded-xl border border-gray-200 bg-white px-3 py-2 sm:px-4 sm:py-3 shadow-sm">
          <div class="flex min-w-0 items-center gap-3">
            {{-- “Avatar” del tipo --}}
            <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-md bg-gray-100 text-[10px] font-extrabold text-gray-700">
              {{ strtoupper(substr(($doc->mime_type ?? 'DOC'), 0, 3)) }}
            </span>

            <div class="min-w-0">
              {{-- Nombre / etiqueta visible --}}
              <div class="truncate text-sm font-medium text-gray-900">
                {{ $doc->label ?: $doc->original_name }}
              </div>

              {{-- Meta: categoría · fecha subida --}}
              <div class="flex flex-wrap items-center gap-2 text-xs text-gray-500">
                <span>{{ $doc->category ?: 'No especificado' }}</span>
                @if(!empty($doc->label))
                  <span class="inline-flex items-center rounded-full bg-purple-100 px-2 py-0.5 text-purple-700 ring-1 ring-purple-200">
                    {{ $doc->label }}
                  </span>
                @endif
                <span>· Subido: {{ optional($doc->created_at)->format('d-m-Y H:i') }}</span>
              </div>
            </div>
          </div>

          {{-- Acciones --}}
          <div class="ml-3 flex shrink-0 items-center gap-1 sm:gap-2">
            {{-- Ver (modal) --}}
            <button type="button"
                    class="rounded-md border px-2.5 py-1.5 text-sm hover:bg-gray-50"
                    @click="window.dispatchEvent(new CustomEvent('doc:show', { detail: {{ json_encode([
                        'id' => $doc->id,
                        'name' => $doc->original_name,
                        'label' => $doc->label,
                        'category' => $doc->category,
                        'description' => $doc->description,
                        'size' => $doc->size,
                        'mime' => $doc->mime_type,
                        'created' => optional($doc->created_at)->format('d-m-Y H:i'),
                        'download' => route('documents.download', $doc),
                    ]) }} }))">
              👁️ Ver
            </button>

            {{-- Compartir (copia link de descarga) --}}
            <button type="button"
                    class="rounded-md border px-2.5 py-1.5 text-sm hover:bg-gray-50"
                    data-share="{{ route('documents.download', $doc) }}"
                    onclick="copyShareLink(this)">
              🔗 Compartir
            </button>

            {{-- Descargar --}}
<a href="{{ route('documents.download', $doc) }}"
   target="_blank" rel="noopener"
   onclick="setTimeout(()=>{ window.location.href='{{ route('portal.home') }}' }, 1800)"
   class="rounded-md border px-2.5 py-1.5 text-sm hover:bg-gray-50">
  Descargar
</a>

            {{-- Eliminar --}}
<form action="{{ route('documents.destroy', $doc) }}" method="POST" class="delete-doc-form inline">
  @csrf
  @method('DELETE')
  <button type="submit"
          class="rounded-md border px-2.5 py-1.5 text-sm text-red-600 hover:bg-red-50">
    Eliminar
  </button>
</form>
          </div>
        </div>
      @empty
        <div class="rounded-xl border border-dashed border-purple-300 bg-white px-4 py-8 text-center text-purple-800">
          Aún no tienes documentos.
        </div>
      @endforelse
    </div>

    <div>
      {{ $docs->withQueryString()->links() }}
    </div>
  </div>
</div>

{{-- Modal “Ver detalle” (Alpine-less: pequeño vanilla + eventos) --}}
<div id="docDetailModal" class="fixed inset-0 z-[70] hidden items-center justify-center">
  <div class="absolute inset-0 bg-black/50" onclick="closeDocModal()"></div>
  <div class="relative w-full max-w-lg rounded-2xl bg-white shadow-2xl ring-1 ring-black/5 overflow-hidden">
    <div class="flex items-center justify-between px-5 py-3 bg-gradient-to-r from-purple-600 via-fuchsia-600 to-purple-500 text-white">
      <div class="font-semibold">Detalle del documento</div>
      <button class="p-2 hover:bg-white/10 rounded-md" onclick="closeDocModal()">✕</button>
    </div>
    <div class="px-5 py-4 space-y-2 text-sm">
      <div><span class="text-gray-500">Nombre:</span> <span id="doc-name" class="font-medium text-gray-900"></span></div>
      <div class="flex flex-wrap gap-2">
        <span class="text-gray-500">Categoría:</span>
        <span id="doc-category" class="inline-flex items-center rounded-full bg-purple-100 px-2 py-0.5 text-purple-700 ring-1 ring-purple-200">—</span>
      </div>
      <div><span class="text-gray-500">Etiqueta:</span> <span id="doc-label" class="text-gray-900">—</span></div>
      <div><span class="text-gray-500">Tipo:</span> <span id="doc-mime" class="text-gray-900">—</span></div>
      <div><span class="text-gray-500">Tamaño:</span> <span id="doc-size" class="text-gray-900">—</span></div>
      <div><span class="text-gray-500">Subido:</span> <span id="doc-created" class="text-gray-900">—</span></div>
      <div class="pt-2">
        <div class="text-gray-500">Descripción</div>
        <div id="doc-description" class="rounded-md border border-gray-200 bg-gray-50 p-2 text-gray-700 whitespace-pre-wrap">—</div>
      </div>
    </div>
    <div class="flex items-center justify-end gap-2 px-5 pb-4">
<a id="doc-download"
   href="#"
   data-url="{{ route('documents.download', $doc) }}"
   class="rounded-md border px-3 py-1.5 text-sm hover:bg-gray-50">
  Descargar
</a>
<button class="rounded-md border px-3 py-1.5 text-sm" onclick="closeDocModal()">Cerrar</button>

    </div>
  </div>

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const btn = document.getElementById('doc-download');
  let redirectTimer = null;

  btn.addEventListener('click', (e) => {
    e.preventDefault();
    const url = btn.dataset.url;
    if (!url) return;

    // 1) dispara la descarga en nueva pestaña (más compatible)
    window.open(url, '_blank', 'noopener');

    // 2) redirige esta pestaña
    clearTimeout(redirectTimer);
    redirectTimer = setTimeout(() => {
      window.location.href = "{{ route('portal.home') }}";
    }, 2000);
  });
});
</script>

<script>
  // Copiar link de compartir (usa la ruta de descarga)
  function copyShareLink(btn){
    const url = btn.getAttribute('data-share');
    if(!url) return;
    navigator.clipboard.writeText(url).then(()=>{
      btn.textContent = '✓ Copiado';
      setTimeout(()=>{ btn.textContent = '🔗 Compartir'; }, 1400);
    }).catch(()=>{ alert('No se pudo copiar el enlace'); });
  }

  // Modal detalle (escucha evento con el payload del doc)
  window.addEventListener('doc:show', (e) => {
    const d = e.detail || {};
    document.getElementById('doc-name').textContent = d.label || d.name || '—';
    document.getElementById('doc-category').textContent = d.category || 'No especificado';
    document.getElementById('doc-label').textContent = d.label || '—';
    document.getElementById('doc-mime').textContent = d.mime || '—';
    document.getElementById('doc-size').textContent = humanSize(d.size);
    document.getElementById('doc-created').textContent = d.created || '—';
    document.getElementById('doc-description').textContent = d.description || '—';
    document.getElementById('doc-download').setAttribute('href', d.download || '#');

    const modal = document.getElementById('docDetailModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
  });

  function closeDocModal(){
    const modal = document.getElementById('docDetailModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
  }

  function humanSize(bytes){
    if(bytes == null) return '—';
    const u = ['B','KB','MB','GB']; let i = 0;
    while(bytes >= 1024 && i < u.length - 1){ bytes /= 1024; i++; }
    return (Math.round(bytes * 10) / 10) + ' ' + u[i];
  }
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.delete-doc-form').forEach(form => {
    form.addEventListener('submit', function (e) {
      e.preventDefault(); // evita el envío inmediato

      Swal.fire({
        title: '¿Eliminar este documento?',
        text: "Esta acción no se puede deshacer.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
      }).then((result) => {
        if (result.isConfirmed) {
          form.submit(); // envía el formulario si confirman
        }
      });
    });
  });
});
</script>
@endpush
@endsection
