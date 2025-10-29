@extends('layouts.app')

@section('title', 'Información de seguridad — Clínicas IST')

@section('content')
<div class="min-h-[88vh] bg-gradient-to-b from-violet-100 to-white dark:from-gray-900 dark:to-gray-900">
  <div class="max-w-4xl mx-auto px-6 py-10">
    {{-- Contenedor centrado, sin opción de cerrar --}}
    <div id="modalInfoSeguridad"
         class="mx-auto bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-xl w-full md:w-[70%] max-h-[80vh] overflow-y-auto select-none">

      <h2 class="text-lg font-bold text-red-600 mb-2">Disclaimer</h2>
      <p class="mb-3">
        <strong>Uso de Plataforma de Imagenología</strong><br>
        <strong>Clínicas IST – Plataforma GALEN</strong>
      </p>

      <h3 class="text-base font-bold text-cyan-700 mt-4">1. Introducción</h3>
      <p>
        Esta plataforma digital permite a los pacientes de <strong>Clínicas IST</strong>
        acceder de forma segura a sus estudios de imagenología.<br>
        La solución tecnológica es provista por <strong>GALEN</strong>, especializada en sistemas RIS/PACS para salud.
      </p>

      <h3 class="text-base font-bold text-cyan-700 mt-4">2. Propósito del Acceso</h3>
      <p>
        El acceso a esta plataforma está <strong>estrictamente limitado a la visualización de estudios</strong>
        de imagenología realizados en las Clínicas IST.
      </p>
      <ul class="space-y-2 ml-4 mt-2">
        <li class="flex items-start"><span class="mr-2">❌</span>
          <span>No permite la edición, modificación ni interpretación clínica directa de los estudios.</span>
        </li>
        <li class="flex items-start"><span class="mr-2">❌</span>
          <span>No está habilitada para la descarga de imágenes ni para su uso en otros entornos médicos.</span>
        </li>
        <li class="flex items-start"><span class="mr-2">✅</span>
          <span>Su propósito exclusivo es la <strong>consulta visual</strong> de los exámenes realizados.</span>
        </li>
      </ul>

      <h3 class="text-base font-bold text-cyan-700 mt-4">3. Modo de Uso</h3>
      <ul class="list-disc list-inside ml-4">
        <li><strong>Usuario:</strong> su RUT (sin puntos, con guión).</li>
        <li><strong>Contraseña inicial:</strong> primeros 6 dígitos del RUT (sin guión ni dígito verificador).</li>
      </ul>

      <p class="mt-2 text-yellow-600 font-semibold">⚠️ Importante:</p>
      <p>
        En el primer inicio de sesión, el sistema solicitará el cambio obligatorio de contraseña por seguridad.
      </p>

      <h3 class="text-base font-bold text-cyan-700 mt-4">4. Condiciones Generales de Uso</h3>
      <p class="mt-1"><strong>a. Acceso Personalizado:</strong> exclusivo para el paciente autorizado.</p>
      <p><strong>b. Protección de Datos:</strong> conforme a Ley N°19.628 y N°20.584.</p>
      <p><strong>c. Uso Informativo:</strong> no reemplaza diagnóstico médico profesional.</p>
      <p><strong>d. Responsabilidad Técnica:</strong> GALEN provee la plataforma. Clínicas IST publica los estudios.</p>
      <p><strong>e. Limitaciones del Servicio:</strong> sujeto a mantenciones o problemas técnicos.</p>

      <h3 class="text-base font-bold text-cyan-700 mt-4">5. Aceptación de Términos</h3>
      <p>Al continuar usando la plataforma, usted acepta estos términos y condiciones.</p>

      <h3 class="text-base font-bold text-cyan-700 mt-4">6. Referencia Legal</h3>
      <p>
        Enmarcado en la Ley <strong>N°20.584</strong>. Texto completo en la
        <a href="https://www.bcn.cl/leychile/navegar?idNorma=1039348" target="_blank"
           class="text-blue-600 underline hover:text-blue-800">Biblioteca del Congreso Nacional de Chile</a>.
      </p>

      <h3 class="text-base font-bold text-cyan-700 mt-4">7. Contacto</h3>
      <p>Para consultas técnicas o problemas de acceso: soporte de <strong>Clínicas IST</strong>.</p>

      <div class="border-t border-gray-200 dark:border-gray-700 mt-6 pt-4 flex justify-center">
        <button type="button" id="btnEntendido"
                class="bg-cyan-700 dark:bg-gray-700 text-white px-8 py-2 rounded-lg font-semibold
                       hover:bg-cyan-800 dark:hover:bg-gray-600 transition">
          Entendido
        </button>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
  // No permitir cerrar con ESC ni clic fuera: NO hay overlay clickeable.
  // Acción "Entendido": ir a la ruta existente con ?ok=1
  document.getElementById('btnEntendido').addEventListener('click', function(){
    window.location.href = "{{ route('validacion.sin') }}?ok=1";
  });
</script>
@endpush
@endsection
