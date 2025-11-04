{{-- resources/views/admin/noticias/_form.blade.php --}}
@csrf

<div class="space-y-6">
  {{-- Título --}}
  <div>
    <label class="block font-semibold mb-1">Título</label>
    <input name="titulo"
           value="{{ old('titulo', $noticia->titulo ?? '') }}"
           class="w-full rounded-xl border p-3"
           required>
    @error('titulo')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
  </div>

  {{-- Bajada --}}
  <div>
    <label class="block font-semibold mb-1">Bajada</label>
    <textarea name="bajada" rows="2"
              class="w-full rounded-xl border p-3">{{ old('bajada', $noticia->bajada ?? '') }}</textarea>
  </div>

  {{-- Contenido con editor reutilizable --}}
  <x-admin.richtext
      name="contenido"
      label="Contenido"
      :value="old('contenido', $noticia->contenido ?? '')"
      placeholder="Escribe el contenido completo de la noticia..."
      toolbar="basic"
  />

{{-- Imagen --}}
<div>
  <label class="block font-semibold mb-2 text-gray-700">Imagen (opcional)</label>
  <div 
    id="dropzone" 
    class="relative border-2 border-dashed border-gray-300 rounded-lg p-6 text-center bg-gray-50 cursor-pointer transition hover:bg-gray-100"
  >
    <span id="dropzone-text" class="text-gray-500 text-sm">
      Arrastra tu imagen aquí o haz clic para seleccionar
    </span>
    <input 
      type="file" 
      name="imagen" 
      accept="image/*" 
      class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" 
      id="imagen-input"
    >
  </div>
  @error('imagen')
    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
  @enderror
</div>

@push('scripts')
<script>
  const fileInput = document.getElementById('imagen-input');
  const dropzone = document.getElementById('dropzone');
  const dropzoneText = document.getElementById('dropzone-text');

  // Clic en el dropzone activa el input
  dropzone.addEventListener('click', () => fileInput.click());

  // Manejo de selección (clic o drag)
  fileInput.addEventListener('change', (e) => {
    if (e.target.files && e.target.files[0]) {
      dropzoneText.textContent = e.target.files[0].name;
      dropzone.classList.replace('border-dashed', 'border-solid');
      dropzone.classList.add('border-teal-500', 'bg-teal-50');
    }
  });

  // Opcional: Soporte básico de drag & drop
  dropzone.addEventListener('dragover', (e) => {
    e.preventDefault();
    dropzone.classList.add('bg-gray-100');
  });

  dropzone.addEventListener('dragleave', () => {
    dropzone.classList.remove('bg-gray-100');
  });

  dropzone.addEventListener('drop', (e) => {
    e.preventDefault();
    dropzone.classList.remove('bg-gray-100');
    if (e.dataTransfer.files && e.dataTransfer.files[0]) {
      const file = e.dataTransfer.files[0];
      if (file.type.startsWith('image/')) {
        fileInput.files = e.dataTransfer.files;
        dropzoneText.textContent = file.name;
        dropzone.classList.replace('border-dashed', 'border-solid');
        dropzone.classList.add('border-teal-500', 'bg-teal-50');
      }
    }
  });
</script>
@endpush

  {{-- Destacada --}}
  <label class="inline-flex items-center gap-2">
    <input type="checkbox" name="destacada" value="1"
           {{ old('destacada', $noticia->destacada ?? false) ? 'checked' : '' }}
           class="rounded">
    <span>¿Poner esta noticia en el home?</span>
  </label>

  {{-- Botones --}}
  <div class="flex gap-3">
    <button class="rounded-xl bg-purple-900 text-white px-4 py-2 font-semibold">
      {{ isset($noticia) ? 'Actualizar' : 'Guardar' }}
    </button>
    <a href="{{ route('admin.noticias.index') }}" class="rounded-xl border px-4 py-2">Cancelar</a>
  </div>
</div>
