@csrf

{{-- Tarjeta --}}
<div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-5 shadow-sm">
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    {{-- Nombre completo --}}
    <div class="md:col-span-2">
      <label class="block text-sm font-semibold text-purple-900 dark:text-purple-200">Nombre completo</label>
      <input type="text" name="nombre_completo"
             value="{{ old('nombre_completo', $admin->nombre_completo ?? '') }}"
             class="mt-1 w-full rounded-xl border border-purple-200/60 dark:border-purple-800/50
                    bg-white dark:bg-gray-950 text-gray-900 dark:text-gray-100
                    focus:ring-2 focus:ring-purple-400 focus:border-purple-400 px-3 py-2"
             required>
      @error('nombre_completo') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    {{-- Email --}}
    <div>
      <label class="block text-sm font-semibold text-purple-900 dark:text-purple-200">Email</label>
      <input type="email" name="email"
             value="{{ old('email', $admin->email ?? '') }}"
             class="mt-1 w-full rounded-xl border border-purple-200/60 dark:border-purple-800/50
                    bg-white dark:bg-gray-950 text-gray-900 dark:text-gray-100
                    focus:ring-2 focus:ring-purple-400 focus:border-purple-400 px-3 py-2"
             required>
      @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    {{-- RUT --}}
    <div>
      <label class="block text-sm font-semibold text-purple-900 dark:text-purple-200">RUT</label>
      <input type="text" name="rut"
             value="{{ old('rut', $admin->rut ?? '') }}"
             class="mt-1 w-full rounded-xl border border-purple-200/60 dark:border-purple-800/50
                    bg-white dark:bg-gray-950 text-gray-900 dark:text-gray-100
                    focus:ring-2 focus:ring-purple-400 focus:border-purple-400 px-3 py-2"
             required>
      @error('rut') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    {{-- Usuario --}}
    <div>
      <label class="block text-sm font-semibold text-purple-900 dark:text-purple-200">Usuario</label>
      <input type="text" name="user"
             value="{{ old('user', $admin->user ?? '') }}"
             class="mt-1 w-full rounded-xl border border-purple-200/60 dark:border-purple-800/50
                    bg-white dark:bg-gray-950 text-gray-900 dark:text-gray-100
                    focus:ring-2 focus:ring-purple-400 focus:border-purple-400 px-3 py-2"
             required>
      @error('user') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    {{-- Rol --}}
    <div>
      <label class="block text-sm font-semibold text-purple-900 dark:text-purple-200">Rol</label>
      <select name="rol"
              class="mt-1 w-full rounded-xl border border-purple-200/60 dark:border-purple-800/50
                     bg-white dark:bg-gray-950 text-gray-900 dark:text-gray-100
                     focus:ring-2 focus:ring-purple-400 focus:border-purple-400 px-3 py-2"
              required>
        @php
          $val = old('rol', $admin->rol ?? '');
          $roles = ['admin' => 'Administrador', 'editor' => 'Editor', 'viewer' => 'Viewer'];
        @endphp
        <option value="" disabled {{ $val ? '' : 'selected' }}>Seleccione un rol…</option>
        @foreach($roles as $k => $label)
          <option value="{{ $k }}" {{ $val === $k ? 'selected' : '' }}>{{ $label }}</option>
        @endforeach
      </select>
      @error('rol') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    {{-- Especialidad (opcional) --}}
    <div class="md:col-span-2">
      <label class="block text-sm font-semibold text-purple-900 dark:text-purple-200">Especialidad (opcional)</label>
      <input type="text" name="especialidad"
             value="{{ old('especialidad', $admin->especialidad ?? '') }}"
             class="mt-1 w-full rounded-xl border border-purple-200/60 dark:border-purple-800/50
                    bg-white dark:bg-gray-950 text-gray-900 dark:text-gray-100
                    focus:ring-2 focus:ring-purple-400 focus:border-purple-400 px-3 py-2">
      @error('especialidad') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    {{-- Contraseña --}}
    <div>
      <label class="block text-sm font-semibold text-purple-900 dark:text-purple-200">
        Contraseña
        @if(($mode ?? '') === 'edit')
          <span class="text-xs text-gray-500"> (opcional)</span>
        @endif
      </label>
      <div class="relative">
        <input :type="window.__showPwd1 ? 'text' : 'password'" name="password" id="pwd1"
               class="mt-1 w-full rounded-xl border border-purple-200/60 dark:border-purple-800/50
                      bg-white dark:bg-gray-950 text-gray-900 dark:text-gray-100
                      focus:ring-2 focus:ring-purple-400 focus:border-purple-400 px-3 py-2 pr-10"
               {{ ($mode ?? '') === 'create' ? 'required' : '' }}>
        <button type="button" onclick="window.__showPwd1 = !window.__showPwd1; document.getElementById('pwd1').type = window.__showPwd1 ? 'text' : 'password';"
                class="absolute right-2 top-2.5 text-sm text-purple-800/70 dark:text-purple-200/80 hover:underline">
          Ver
        </button>
      </div>
      @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    {{-- Confirmar contraseña --}}
    <div>
      <label class="block text-sm font-semibold text-purple-900 dark:text-purple-200">
        Confirmar contraseña
        @if(($mode ?? '') === 'edit')
          <span class="text-xs text-gray-500"> (opcional)</span>
        @endif
      </label>
      <div class="relative">
        <input :type="window.__showPwd2 ? 'text' : 'password'" name="password_confirmation" id="pwd2"
               class="mt-1 w-full rounded-xl border border-purple-200/60 dark:border-purple-800/50
                      bg-white dark:bg-gray-950 text-gray-900 dark:text-gray-100
                      focus:ring-2 focus:ring-purple-400 focus:border-purple-400 px-3 py-2 pr-10"
               {{ ($mode ?? '') === 'create' ? 'required' : '' }}>
        <button type="button" onclick="window.__showPwd2 = !window.__showPwd2; document.getElementById('pwd2').type = window.__showPwd2 ? 'text' : 'password';"
                class="absolute right-2 top-2.5 text-sm text-purple-800/70 dark:text-purple-200/80 hover:underline">
          Ver
        </button>
      </div>
    </div>

    {{-- Activo --}}
    <div class="md:col-span-2">
      <label class="block text-sm font-semibold text-purple-900 dark:text-purple-200 mb-1">Activo</label>
      <label class="inline-flex items-center gap-3 select-none">
        <input type="checkbox" name="activo" value="1"
               @checked(old('activo', ($admin->activo ?? true) ? 1 : 0))
               class="peer sr-only">
        <span class="w-12 h-7 rounded-full border border-purple-200/70 dark:border-purple-800/60
                     bg-gray-200 dark:bg-gray-700 relative transition
                     after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:h-6 after:w-6
                     after:rounded-full after:bg-white after:shadow after:transition
                     peer-checked:bg-purple-600 peer-checked:after:translate-x-5"></span>
        <span class="text-sm text-gray-700 dark:text-gray-300 peer-checked:text-purple-900 dark:peer-checked:text-gray-100">
          Habilitar acceso del administrador
        </span>
      </label>
    </div>
  </div>
</div>

{{-- Acciones --}}
<div class="mt-4 flex items-center justify-end gap-2">
  <a href="{{ route('admin.administradores.index') }}"
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
