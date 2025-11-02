{{-- resources/views/components/review-popup.blade.php --}}
@if (auth()->check() && !(auth()->user()->review || \App\Models\Review::where('user_id', auth()->id())->exists()))
<div
  x-data="reviewPopup()"
  x-init="init()"
  x-cloak
  @keydown.escape.window="closePopup()"
  :class="open ? 'fixed inset-0 z-50' : ''"   {{-- ← solo cubre pantalla cuando open=true --}}
>
  <!-- Fondo -->
  <div x-show="open" x-transition.opacity class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>

  <!-- Modal -->
  <div x-show="open" x-transition
       x-trap.noscroll="open"
       class="relative z-10 flex items-center justify-center p-4 min-h-screen">
    <div class="w-full max-w-md rounded-xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 shadow-lg overflow-hidden">
      <!-- Encabezado -->
      <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">
          ¿Cómo calificarías tu experiencia?
        </h3>
        <button @click="closePopup()"
                class="text-sm text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-gray-100">
          Cerrar
        </button>
      </div>

      <!-- Cuerpo -->
      <div class="p-4 space-y-4">
        <!-- Estrellas -->
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

        <!-- Anónimo -->
        <label class="inline-flex items-center gap-2 select-none">
          <input type="checkbox" x-model="anonimo"
                 class="w-4 h-4 rounded border-gray-300 text-purple-700 focus:ring-purple-500 dark:bg-gray-800 dark:border-gray-600">
          <span class="text-sm text-gray-700 dark:text-gray-300">Valorar como anónimo</span>
        </label>

        <!-- Comentario -->
        <textarea
          x-model="comment"
          placeholder="Comentarios (opcional)"
          class="w-full rounded-lg border-2 border-purple-200/70 bg-white text-gray-900 placeholder:text-gray-400
                 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200 p-2 text-sm resize-none focus:outline-none
                 focus:ring-2 focus:ring-purple-500/40 focus:border-purple-600"
          rows="3"></textarea>

        <!-- Botones -->
        <div class="flex justify-end gap-2 pt-2">
          <button type="button" @click="closePopup()"
                  class="px-4 py-2 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800">
            Ahora no
          </button>
          <button type="button" @click="submitReview()"
                  class="px-4 py-2 rounded-lg bg-purple-900 text-white hover:bg-purple-800 disabled:opacity-60"
                  :disabled="submitting">
            <span x-show="!submitting">Enviar</span>
            <span x-show="submitting">Enviando…</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  function reviewPopup() {
    return {
      open: false,
      rating: 0,
      tempRating: 0,
      comment: '',
      anonimo: false,
      submitting: false,

      init() {
        // Evitar mostrar de nuevo si ya lo cerró (opcional)
        if (localStorage.getItem('review_popup_closed') === '1') return;

        setTimeout(() => { this.open = true; }, 10000); // abre a los 10s
      },
      setRating(star) {
        this.rating = star;
        this.tempRating = star;
      },
      async submitReview() {
        if (this.rating < 1) {
          alert('Por favor, selecciona una calificación.');
          return;
        }
        this.submitting = true;

        try {
          const formData = new FormData();
          formData.append('rating', this.rating);
          formData.append('comment', this.comment);
          formData.append('anonimo', this.anonimo ? 1 : 0);
          formData.append('_token', document.querySelector('meta[name=csrf-token]').content);

          const res = await fetch('{{ route('reviews.store') }}', {
            method: 'POST',
            headers: { 'Accept': 'application/json' },
            body: formData
          });

          if (!res.ok) {
            const err = await res.json().catch(() => ({}));
            throw new Error(err.message || 'No se pudo enviar la calificación.');
          }

          this.open = false;
          localStorage.setItem('review_popup_closed', '1');
          // location.reload(); // si quieres reflejar cambios
        } catch (e) {
          alert(e.message || 'Error de conexión. Intenta más tarde.');
        } finally {
          this.submitting = false;
        }
      },
      closePopup() {
        this.open = false;
        localStorage.setItem('review_popup_closed', '1');
      }
    }
  }
</script>
@endif
