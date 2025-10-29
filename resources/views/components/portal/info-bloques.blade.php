{{-- resources/views/components/portal/info-top-right.blade.php --}}
<aside
  class="hidden lg:flex flex-col gap-5 absolute top-6 left-6 w-[330px] z-30"
  aria-label="Información de contacto"
>
  {{-- 1) Informaciones --}}
  <div class="flex items-center gap-4 p-5 rounded-2xl bg-white/95 dark:bg-gray-900/95
              shadow-[0_8px_20px_rgba(0,0,0,0.12)] border border-gray-200 dark:border-gray-800">
    <div class="flex-shrink-0 w-14 h-14 rounded-full bg-pink-100 text-pink-600
                grid place-items-center text-2xl shadow">📱</div>
    <div class="leading-tight">
      <div class="text-gray-700 dark:text-gray-300 text-sm">Informaciones</div>
      <div class="text-pink-600 font-extrabold text-xl">600 58 40 000</div>
      <div class="text-gray-500 dark:text-gray-400 text-sm">Horario: 08:30 a 18:00 hrs</div>
      <div class="text-gray-500 dark:text-gray-400 text-sm">Lunes a Viernes</div>
    </div>
  </div>

  {{-- 2) Atención Particular --}}
  <div class="flex items-center gap-4 p-5 rounded-2xl bg-white/95 dark:bg-gray-900/95
              shadow-[0_8px_20px_rgba(0,0,0,0.12)] border border-gray-200 dark:border-gray-800">
    <div class="flex-shrink-0 w-14 h-14 rounded-full bg-gray-100 text-gray-600
                grid place-items-center text-2xl shadow">👤</div>
    <div class="text-gray-700 dark:text-gray-300 text-sm">
      <div class="text-pink-600 font-bold mb-1">Atención Particular</div>
      <ul class="list-disc ml-5 space-y-0.5">
        <li>Centro Médico Viña del Mar</li>
        <li>Centro Médico Concón</li>
      </ul>
    </div>
  </div>
</aside>
