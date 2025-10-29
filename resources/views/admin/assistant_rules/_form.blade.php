@csrf
<div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-5 shadow-sm">
  <div class="grid grid-cols-1 gap-4">
    <div class="grid sm:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-semibold text-purple-900 dark:text-purple-200">Título</label>
        <input name="title" type="text" required
               value="{{ old('title', $rule->title ?? '') }}"
               class="mt-1 w-full rounded-xl border border-purple-200/60 dark:border-purple-800/50 bg-white dark:bg-gray-950 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-purple-400 focus:border-purple-400 px-3 py-2">
      </div>

      <div>
        <label class="block text-sm font-semibold text-purple-900 dark:text-purple-200">Prioridad</label>
        <input name="sort_order" type="number" min="0"
               value="{{ old('sort_order', $rule->sort_order ?? 0) }}"
               class="mt-1 w-32 rounded-xl border border-purple-200/60 dark:border-purple-800/50 bg-white dark:bg-gray-950 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-purple-400 focus:border-purple-400 px-3 py-2">
        <p class="text-xs text-gray-500 mt-1">Menor = se evalúa antes.</p>
      </div>
    </div>

    <div class="grid sm:grid-cols-2 gap-4 items-end">
      <div>
        <label class="block text-sm font-semibold text-purple-900 dark:text-purple-200">Modo de coincidencia</label>
        <select name="match_mode" class="mt-1 w-full rounded-xl border border-purple-200/60 dark:border-purple-800/50 bg-white dark:bg-gray-950 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-purple-400 focus:border-purple-400 px-3 py-2">
          <option value="any" @selected(old('match_mode', $rule->match_mode ?? 'any') === 'any')>Coincide cualquiera</option>
          <option value="all" @selected(old('match_mode', $rule->match_mode ?? 'any') === 'all')>Deben coincidir todas</option>
        </select>
      </div>

      <label class="inline-flex items-center gap-3">
        <input type="checkbox" name="use_regex" value="1" @checked(old('use_regex', $rule->use_regex ?? false)) class="peer sr-only">
        <span class="w-12 h-7 rounded-full border border-purple-200/70 dark:border-purple-800/60 bg-gray-200 dark:bg-gray-700 relative transition after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:h-6 after:w-6 after:rounded-full after:bg-white after:shadow after:transition peer-checked:bg-purple-600 peer-checked:after:translate-x-5"></span>
        <span class="text-sm text-gray-700 dark:text-gray-300">Usar Regex</span>
      </label>
    </div>

    <div class="relative group">
      <label class="block text-sm font-semibold text-purple-900 dark:text-purple-200">Palabras clave o patrones</label>
      <textarea name="keywords" rows="4" required
                class="mt-1 w-full rounded-xl border border-purple-200/60 dark:border-purple-800/50 bg-white dark:bg-gray-950 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-purple-400 focus:border-purple-400 px-3 py-2"
                placeholder="Separar por coma o por línea. Ej: cuenta, credenciales, registrar&#10;Si usas Regex, escribe un patrón por línea.">{{ old('keywords', $rule->keywords ?? '') }}</textarea>

      {{-- Icono "i" con hover --}}
{{-- Icono "i" con hover (explicación + ejemplo) --}}
<div class="absolute -top-2 right-0 translate-y-[-50%]">
  <div class="relative group">
    {{-- Icono --}}
    <span
      class="inline-flex h-6 w-6 items-center justify-center rounded-full
             bg-purple-100 text-purple-900 border border-purple-200
             text-xs font-bold select-none cursor-default shadow-sm">
      i
    </span>

    {{-- Tooltip --}}
    <div
      class="absolute right-0 mt-2 w-96 rounded-xl border border-gray-200 dark:border-gray-700
             bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-200 text-sm p-4 shadow-2xl
             opacity-0 scale-95 translate-y-1 group-hover:opacity-100 group-hover:scale-100
             group-hover:translate-y-0 transition pointer-events-none group-hover:pointer-events-auto z-[9999]"
    >
      <p class="font-semibold mb-1 text-purple-900 dark:text-purple-200">
        💡 Palabras clave o expresiones Regex
      </p>
      <p class="mb-2">
        El asistente usará estas palabras o patrones para decidir qué respuesta mostrar.
      </p>

      <ul class="list-disc ml-5 space-y-1">
        <li><strong>ANY:</strong> coincide si el mensaje del usuario contiene <em>cualquiera</em> de las palabras.</li>
        <li><strong>ALL:</strong> deben aparecer todas las palabras para coincidir.</li>
        <li><strong>Regex:</strong> puedes usar expresiones regulares avanzadas (una por línea).</li>
      </ul>

      <div class="mt-3 p-2 bg-purple-50 dark:bg-purple-950/40 rounded-lg text-xs border border-purple-200 dark:border-purple-700">
        <p class="font-semibold mb-1 text-purple-900 dark:text-purple-200">🧩 Ejemplo:</p>
        <pre class="whitespace-pre-wrap text-gray-800 dark:text-gray-100">
crear cuenta
registrarme
credenciales
        </pre>
        <p class="mt-1">
          Si el usuario escribe <em>“¿Cómo creo una cuenta?”</em>, el asistente responderá con la regla configurada.
        </p>
      </div>
    </div>
  </div>
</div>
    </div>

    <div>
      <label class="block text-sm font-semibold text-purple-900 dark:text-purple-200">Respuesta</label>
      <textarea name="response" rows="6" required
                class="mt-1 w-full rounded-xl border border-purple-200/60 dark:border-purple-800/50 bg-white dark:bg-gray-950 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-purple-400 focus:border-purple-400 px-3 py-2"
                placeholder="Texto o HTML que enviará el asistente.">{{ old('response', $rule->response ?? '') }}</textarea>
    </div>

    <div class="flex items-center justify-between">
      <label class="inline-flex items-center gap-3">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', ($rule->is_active ?? true))) class="peer sr-only">
        <span class="w-12 h-7 rounded-full border border-purple-200/70 dark:border-purple-800/60 bg-gray-200 dark:bg-gray-700 relative transition after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:h-6 after:w-6 after:rounded-full after:bg-white after:shadow after:transition peer-checked:bg-purple-600 peer-checked:after:translate-x-5"></span>
        <span class="text-sm text-gray-700 dark:text-gray-300">Activa</span>
      </label>
    </div>
  </div>
</div>

<div class="mt-4 flex items-center justify-end gap-2">
  <a href="{{ route('admin.assistant_rules.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-purple-200/60 dark:border-purple-800/50 bg-white dark:bg-gray-950 px-4 py-2 text-sm text-purple-900 dark:text-gray-100 hover:bg-purple-50 dark:hover:bg-gray-900">Cancelar</a>
  <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-purple-900 px-4 py-2 text-sm font-semibold text-white hover:opacity-90 shadow">Guardar</button>
</div>
