@csrf

{{-- Tarjeta del formulario --}}
<div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-5 shadow-sm">

  {{-- Campos --}}
  <div class="grid grid-cols-1 gap-4">

    {{-- Pregunta --}}
    <div>
      <label class="block text-sm font-semibold text-purple-900 dark:text-purple-200">Pregunta</label>
      <input type="text" name="question"
             value="{{ old('question', $faq->question ?? '') }}"
             class="mt-1 w-full rounded-xl border border-purple-200/60 dark:border-purple-800/50
                    bg-white dark:bg-gray-950 text-gray-900 dark:text-gray-100
                    focus:ring-2 focus:ring-purple-400 focus:border-purple-400 px-3 py-2"
             placeholder="Ej: ¿Cómo descargo mis resultados?" required>
      @error('question')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
      @enderror
    </div>

    {{-- Respuesta --}}
    <div>
      <label class="block text-sm font-semibold text-purple-900 dark:text-purple-200">Respuesta</label>
      <textarea name="answer" rows="6"
                class="mt-1 w-full rounded-xl border border-purple-200/60 dark:border-purple-800/50
                       bg-white dark:bg-gray-950 text-gray-900 dark:text-gray-100
                       focus:ring-2 focus:ring-purple-400 focus:border-purple-400 px-3 py-2"
                placeholder="Escribe la respuesta que verá el paciente…"
                required>{{ old('answer', $faq->answer ?? '') }}</textarea>
      @error('answer')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
      @enderror
    </div>

    {{-- Fila: Orden + Visible --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
      <div>
        <label class="block text-sm font-semibold text-purple-900 dark:text-purple-200">Orden</label>
        <input type="number" min="0" name="sort_order"
               value="{{ old('sort_order', $faq->sort_order ?? ($faq->order ?? 0)) }}"
               class="mt-1 w-32 rounded-xl border border-purple-200/60 dark:border-purple-800/50
                      bg-white dark:bg-gray-950 text-gray-900 dark:text-gray-100
                      focus:ring-2 focus:ring-purple-400 focus:border-purple-400 px-3 py-2">
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Menor aparece primero.</p>
      </div>

      <div class="sm:col-span-2">
        <label class="block text-sm font-semibold text-purple-900 dark:text-purple-200 mb-1">Visible</label>
        {{-- Toggle "visible" estilizado --}}
        <label class="inline-flex items-center gap-3 select-none">
          <input type="checkbox" name="is_active" value="1"
                 @checked(old('is_active', ($faq->is_active ?? ($faq->visible ?? true)) ? 1 : 0))
                 class="peer sr-only">
          <span class="w-12 h-7 rounded-full border border-purple-200/70 dark:border-purple-800/60
                       bg-gray-200 dark:bg-gray-700 relative transition
                       after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:h-6 after:w-6
                       after:rounded-full after:bg-white after:shadow after:transition
                       peer-checked:bg-purple-600 peer-checked:after:translate-x-5"></span>
          <span class="text-sm text-gray-700 dark:text-gray-300 peer-checked:text-purple-900 dark:peer-checked:text-gray-100">
            Activa para mostrar en el chat
          </span>
        </label>
      </div>
    </div>

  </div>
</div>

{{-- Acciones --}}
<div class="mt-4 flex items-center justify-end gap-2">
  <a href="{{ route('admin.faqs.index') }}"
     class="inline-flex items-center gap-2 rounded-xl border border-purple-200/60 dark:border-purple-800/50
            bg-white dark:bg-gray-950 px-4 py-2 text-sm text-purple-900 dark:text-gray-100
            hover:bg-purple-50 dark:hover:bg-gray-900">
    Cancelar
  </a>

  <button type="submit"
          class="inline-flex items-center gap-2 rounded-xl bg-purple-900 px-4 py-2 text-sm font-semibold text-white
                 hover:opacity-90 shadow">
    Guardar
  </button>
</div>
