@php
  $hasReviewed = (bool) (auth()->user()->review ?? false);
@endphp

@auth
<div
  x-data="calificacion({
    hasReviewed: @json($hasReviewed),
    storeUrl: '{{ route('reviews.store') }}',
    updateUrl: '{{ route('reviews.update.mine') }}'
  })"
  x-init="init()"
>

  {{-- ⭐ Botón flotante SIEMPRE visible --}}
  <button
    type="button"
    title="Calificar app"
    aria-label="Calificar app"
    class="fixed bottom-48 right-6 z-[100] flex items-center justify-center w-12 h-12 rounded-full
           bg-yellow-400 hover:bg-yellow-500 text-white shadow-lg shadow-yellow-500/30
           transition-all focus:outline-none focus:ring-2 focus:ring-yellow-300"
    @click.stop="open = true"
  >
    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="currentColor" viewBox="0 0 20 20">
      <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.802 2.036a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.802-2.036a1 1 0 00-1.176 0L6.606 16.282c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.97 8.72c-.783-.57-.38-1.81.588-1.81H7.02a1 1 0 00.95-.69l1.079-3.292z"/>
    </svg>
  </button>

  {{-- Overlay --}}
  <div x-show="open" x-cloak x-transition.opacity class="fixed inset-0 z-[90] bg-black/40 backdrop-blur-sm"></div>

  {{-- Modal --}}
  <div x-show="open" x-cloak x-transition
       class="fixed inset-0 z-[95] flex items-center justify-center p-4">
    <div class="w-full max-w-md rounded-xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 shadow-lg overflow-hidden">

      {{-- Encabezado --}}
      <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">
          <span x-text="hasReviewed ? 'Actualizar tu calificación' : '¿Cómo calificarías tu experiencia?'"></span>
        </h3>
        <button @click="closePopup()"
                class="text-sm text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-gray-100">
          ✕
        </button>
      </div>

      {{-- Cuerpo --}}
      <div class="p-4 space-y-4">
        {{-- Estrellas --}}
        <div class="flex justify-center space-x-1" dir="ltr" @mouseleave="tempRating = rating">
          <template x-for="star in [1,2,3,4,5]" :key="star">
            <button type="button"
                    @click="setRating(star)"
                    @mouseover="tempRating = star"
                    :class="star <= (tempRating || rating) ? 'text-yellow-400' : 'text-gray-400 dark:text-gray-500'"
                    class="text-2xl focus:outline-none transition-colors"
                    :aria-label="'Calificar con ' + star + ' estrellas'">
              ★
            </button>
          </template>
        </div>

        {{-- Comentario --}}
        <textarea
          x-model="comment"
          placeholder="Comentarios (opcional)"
          class="w-full rounded-lg border-2 border-purple-200/70 bg-white text-gray-900 placeholder:text-gray-400
                 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200 p-2 text-sm resize-none focus:outline-none
                 focus:ring-2 focus:ring-purple-500/40 focus:border-purple-600"
          rows="3"></textarea>

        {{-- Botones --}}
        <div class="flex justify-end gap-2 pt-2">
          <button type="button" @click="closePopup()"
                  class="px-4 py-2 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800">
            Cancelar
          </button>
          <button type="button" @click="submitReview()"
                  class="px-4 py-2 rounded-lg bg-purple-900 text-white hover:bg-purple-800 disabled:opacity-60"
                  :disabled="submitting">
            <span x-show="!submitting" x-text="hasReviewed ? 'Actualizar' : 'Enviar'"></span>
            <span x-show="submitting">Enviando…</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- CDNs y JS del componente vía stacks (evita duplicados) --}}
@once
  @push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  @endpush

@push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    // Requiere Alpine cargado en tu layout (vía Vite o CDN con defer)
    document.addEventListener('alpine:init', () => {
      Alpine.data('calificacion', (opts) => ({
        open: false,
        hasReviewed: !!opts.hasReviewed,
        storeUrl: opts.storeUrl,
        updateUrl: opts.updateUrl,

        rating: 0,
        tempRating: 0,
        comment: '',
        submitting: false,

        init() {
          // No auto-abrir
        },

        setRating(star) { this.rating = star; this.tempRating = star; },
        closePopup() { this.open = false; },

        async submitReview() {
          if (this.rating < 1) return this.alertError('Por favor, selecciona una calificación.');
          this.submitting = true;
          try {
            const fd = new FormData();
            fd.append('rating', this.rating);
            fd.append('comment', this.comment);
            fd.append('_token', document.querySelector('meta[name=csrf-token]').content);

            let url = this.storeUrl;
            let method = 'POST';
            if (this.hasReviewed) {
              url = this.updateUrl;
              fd.append('_method', 'PATCH');
            }

            const res = await fetch(url, { method, headers: { 'Accept': 'application/json' }, body: fd });
            if (!res.ok) throw new Error('No se pudo guardar la calificación.');

            this.open = false;
            this.hasReviewed = true;
            this.alertOk('¡Listo!', this.hasReviewed ? 'Calificación actualizada.' : '¡Gracias por tu calificación!');
          } catch (e) {
            this.alertError(e.message || 'Error de conexión. Intenta más tarde.');
          } finally {
            this.submitting = false;
          }
        },

        alertOk(title, text) {
          if (window.Swal && typeof Swal.fire === 'function') {
            Swal.fire({
              icon: 'success',
              title,
              text,
              confirmButtonColor: '#4f46e5',
              background: document.documentElement.classList.contains('dark') ? '#1f2937' : '#fff',
              color: document.documentElement.classList.contains('dark') ? '#e5e7eb' : '#111827',
              timer: 3000,                 // ⏱ dura 3 segundos
              showConfirmButton: false,    // sin botón “OK”
              timerProgressBar: true
            });
          } else {
            alert((title ? title + '\n' : '') + (text || ''));
          }
        },

        alertError(text) {
          if (window.Swal && typeof Swal.fire === 'function') {
            Swal.fire({
              icon: 'error',
              title: 'Ups…',
              text,
              confirmButtonColor: '#ef4444',
              background: document.documentElement.classList.contains('dark') ? '#1f2937' : '#fff',
              color: document.documentElement.classList.contains('dark') ? '#e5e7eb' : '#111827',
              timer: 3000,                 // ⏱ dura 3 segundos
              showConfirmButton: false,    // sin botón
              timerProgressBar: true
            });
          } else {
            alert(text);
          }
        },
      }));
    });
  </script>
@endpush

@endonce
@endauth
