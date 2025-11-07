<div x-data="uploadDocSimple()" class="relative">
  <!-- Botón -->
<!-- Botón (dispara el modal) -->
<button
  @click="open = true"
  type="button"
  class="group relative inline-flex items-center justify-center gap-2 px-5 py-2.5
         rounded-full font-semibold text-white transition-all duration-300
         bg-gradient-to-r from-purple-900 via-purple-600 to-purple-700
         hover:from-purple-700 hover:to-purple-700
         shadow-md hover:shadow-lg
         focus:outline-none focus:ring-2 focus:ring-purple-300">

  <!-- Ícono fijo -->
  <svg xmlns="http://www.w3.org/2000/svg"
       class="h-5 w-5 text-white group-hover:scale-110 transition-transform duration-300"
       viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
    <path d="M12 16a1 1 0 0 1-1-1V8.414L8.707 10.707a1 1 0 1 1-1.414-1.414l4-4 .094-.083a1 1 0 0 1 1.32.083l4 4a1 1 0 1 1-1.414 1.414L13 8.414V15a1 1 0 0 1-1 1Z"/>
    <path d="M6 14a1 1 0 0 0-2 0v3a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3v-3a1 1 0 1 0-2 0v3a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1v-3Z"/>
  </svg>

  <span class="tracking-tight">Subir documento</span>

  <!-- Brillo sutil en hover -->
  <span class="absolute inset-0 rounded-full bg-white/10 opacity-0 group-hover:opacity-100 blur-sm transition duration-300"></span>
</button>


  <!-- Modal -->
  <div x-cloak x-show="open" class="fixed inset-0 z-[60] flex items-center justify-center" aria-modal="true">
    <div class="absolute inset-0 bg-black/50" @click="close()"></div>

    <div x-trap.inert="open" class="relative w-full max-w-xl rounded-2xl bg-white shadow-2xl ring-1 ring-black/5 overflow-hidden">
      <div class="flex items-center justify-between px-5 py-4 bg-gradient-to-r from-purple-600 via-fuchsia-600 to-purple-500 text-white">
        <div class="font-semibold text-lg">Agregar documento</div>
        <button @click="close()" class="p-2 rounded-md hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-white/40">✕</button>
      </div>

      <form
        x-ref="form"
        action="{{ route('documents.store') }}"
        method="POST"
        enctype="multipart/form-data"
        @submit.prevent="submit"
        class="px-5 py-5 space-y-5"
      >
        @csrf

        <!-- Dropzone -->
        <div
          class="rounded-xl border-2 border-dashed p-6 text-center"
          :class="file ? 'border-emerald-300 bg-emerald-50' : 'border-gray-300 hover:border-purple-400'"
          @dragover.prevent="drag = true"
          @dragleave.prevent="drag = false"
          @drop.prevent="handleDrop($event)"
        >
          <input type="file" name="file" x-ref="file" class="hidden" @change="handleFile($event)"
                 accept=".pdf,.jpg,.jpeg,.png,.heic,.doc,.docx,.xls,.xlsx,.txt"/>
          <template x-if="!file">
            <div class="space-y-2">
              <p class="text-sm text-gray-700">Arrastra tu archivo aquí o</p>
              <button type="button" @click="$refs.file.click()" class="px-3 py-1.5 rounded-md bg-gray-900 text-white hover:bg-gray-800">
                Seleccionar archivo
              </button>
              <p class="text-xs text-gray-500">PDF, imágenes o documentos (máx. 20MB).</p>
            </div>
          </template>
          <template x-if="file">
            <div class="space-y-1">
              <p class="text-sm font-medium text-gray-700" x-text="file.name"></p>
              <p class="text-xs text-gray-500" x-text="humanSize(file.size) + ' · ' + (file.type || 'documento')"></p>
              <button type="button" class="text-xs text-red-600 hover:underline" @click="clearFile()">Quitar archivo</button>
            </div>
          </template>
        </div>

        <!-- Metadatos opcionales -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm text-gray-600 mb-1">Categoría</label>
            <select name="category" x-model="category"
class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-800 shadow-sm 
       focus:border-purple-500 focus:ring-2 focus:ring-purple-200 focus:outline-none transition">
              <option value="">— Seleccionar —</option>
              <option value="examen">Examen/Informe</option>
              <option value="receta">Receta</option>
              <option value="certificado">Certificado</option>
              <option value="vacuna">Vacuna</option>
              <option value="otro">Otro</option>
            </select>
          </div>
          <div>
            <label 
            class="block text-sm text-gray-600 mb-1">Etiqueta (opcional)</label>
            <input type="text" name="label" x-model="label"
class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-800 shadow-sm 
       focus:border-purple-500 focus:ring-2 focus:ring-purple-200 focus:outline-none transition"
                   placeholder="Ej: Resultado colesterol"/>
          </div>
          <div class="sm:col-span-2">
            <label class="block text-sm text-gray-600 mb-1">Descripción (opcional)</label>
            <textarea name="description" x-model="description" rows="3"
class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-800 shadow-sm 
       focus:border-purple-500 focus:ring-2 focus:ring-purple-200 focus:outline-none transition"
                      placeholder="Notas que quieras agregar"></textarea>
          </div>
        </div>

        <!-- Errores -->
        <template x-if="error">
          <div class="rounded-lg border border-red-200 bg-red-50 text-red-700 px-3 py-2 text-sm" x-text="error"></div>
        </template>

        <!-- Footer -->
        <div class="flex items-center justify-end gap-3 pt-2">
          <button type="button" @click="close()" class="px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-50">
            Cancelar
          </button>
<button type="submit" 
        :disabled="!canSubmit || loading"
        class="group relative inline-flex items-center justify-center gap-2 px-6 py-2.5
               rounded-full font-semibold text-white transition-all duration-300
               bg-gradient-to-r from-purple-600 via-fuchsia-600 to-purple-700
               hover:from-purple-700 hover:to-fuchsia-700
               shadow-md hover:shadow-lg
               focus:outline-none focus:ring-2 focus:ring-purple-300
               disabled:opacity-60 disabled:cursor-not-allowed">

  <!-- Ícono fijo (visible siempre) -->
  <svg xmlns="http://www.w3.org/2000/svg" 
       class="h-5 w-5 text-white group-hover:scale-110 transition-transform duration-300"
       viewBox="0 0 24 24" fill="currentColor">
    <path d="M12 16a1 1 0 0 1-1-1V8.414L8.707 10.707a1 1 0 1 1-1.414-1.414l4-4 .094-.083a1 1 0 0 1 1.32.083l4 4a1 1 0 1 1-1.414 1.414L13 8.414V15a1 1 0 0 1-1 1Z"/>
    <path d="M6 14a1 1 0 0 0-2 0v3a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3v-3a1 1 0 1 0-2 0v3a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1v-3Z"/>
  </svg>

  <!-- Texto normal -->
  <span x-show="!loading" class="tracking-tight">Subir documento</span>

  <!-- Indicador de carga -->
  <span x-show="loading" class="inline-flex items-center gap-2">
    <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
    </svg>
    <span>Subiendo…</span>
  </span>

  <!-- Brillo animado -->
  <span class="absolute inset-0 rounded-full bg-white/10 opacity-0 group-hover:opacity-100 blur-sm transition duration-300"></span>
</button>

        </div>
      </form>
    </div>
  </div>
</div>

@push('scripts')
<script>
function uploadDocSimple() {
  return {
    open: false, drag: false, file: null, category: '', label: '', description: '', loading: false, error: '',
    get canSubmit(){ return !!this.file; },
    close(){ this.open = false; },
    handleDrop(e){ this.drag=false; const f=e.dataTransfer.files?.[0]; if(f) this.setFile(f); },
    handleFile(e){ const f=e.target.files?.[0]; if(f) this.setFile(f); },
    setFile(f){
      const max=20*1024*1024; if(f.size>max){ this.error='El archivo supera el límite de 20MB.'; return; }
      this.file=f; this.error='';
    },
    clearFile(){ this.file=null; this.$refs.file.value=''; },
    humanSize(b){ if(b==null) return ''; const u=['B','KB','MB','GB']; let i=0; while(b>=1024&&i<u.length-1){ b/=1024;i++; } return b.toFixed(1)+' '+u[i]; },
    async submit(){
      if(!this.canSubmit) return;
      this.loading=true; this.error='';
      try{
        const form=new FormData();
        form.append('_token','{{ csrf_token() }}');
        form.append('file', this.file);
        if(this.category) form.append('category', this.category);
        if(this.label) form.append('label', this.label);
        if(this.description) form.append('description', this.description);

        const res=await fetch(`{{ route('documents.store') }}`, {
          method:'POST', headers:{'X-Requested-With':'XMLHttpRequest'}, body:form
        });
        if(!res.ok){ const data=await res.json().catch(()=>({})); this.error=data.message||'No se pudo subir el documento.'; this.loading=false; return; }

        // ok
        this.loading=false; this.open=false;
        this.clearFile(); this.category=''; this.label=''; this.description='';
        document.dispatchEvent(new CustomEvent('document:uploaded'));
      }catch(e){ this.loading=false; this.error='Error de red. Intenta nuevamente.'; }
    }
  }
}
</script>
@endpush
