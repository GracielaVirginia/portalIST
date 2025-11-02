{{-- resources/views/auth/logout-confirm.blade.php --}}
@extends('layouts.app')

@section('content')
  <div class="min-h-[40vh] flex items-center justify-center">
    <p class="text-gray-600 dark:text-gray-300">Un momento…</p>
  </div>

  @push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  @endpush
  @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
      document.addEventListener('DOMContentLoaded', () => {
        const csrf = @json(csrf_token());
        const storeUrl = @json($storeUrl);

        Swal.fire({
          title: '¿Deseas calificar antes de salir?',
          text: 'Tu opinión nos ayuda a mejorar.',
          icon: 'question',
          showCancelButton: true,
          confirmButtonText: 'Sí, calificar',
          cancelButtonText: 'Salir sin calificar',
          reverseButtons: true,
          confirmButtonColor: '#4f46e5',
          background: document.documentElement.classList.contains('dark') ? '#1f2937' : '#fff',
          color: document.documentElement.classList.contains('dark') ? '#e5e7eb' : '#111827'
        }).then((r) => {
          if (r.isConfirmed) return showReviewForm();
          return forceLogout();
        });

        function forceLogout() {
          const f = document.createElement('form');
          f.method = 'POST';
          f.action = @json(route('logout'));
          f.innerHTML = `<input type="hidden" name="_token" value="${csrf}">`;
          document.body.appendChild(f); f.submit();
        }

        function showReviewForm(){
          let rating = 0;
          const html = `
            <div class="space-y-3">
              <div id="stars" class="flex justify-center gap-1">
                ${[1,2,3,4,5].map(n => `<button type="button" data-s="${n}" class="text-2xl text-gray-400">★</button>`).join('')}
              </div>
              <textarea id="c" rows="3" class="w-full rounded-md border p-2" placeholder="Comentarios (opcional)"></textarea>
            </div>
          `;
          Swal.fire({
            title: 'Califica tu experiencia',
            html,
            showCancelButton: true,
            confirmButtonText: 'Enviar y salir',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#4f46e5',
            background: document.documentElement.classList.contains('dark') ? '#1f2937' : '#fff',
            color: document.documentElement.classList.contains('dark') ? '#e5e7eb' : '#111827',
            didOpen: () => {
              const stars = Swal.getHtmlContainer().querySelectorAll('[data-s]');
              const paint = (n)=> stars.forEach(b=> b.style.color = (parseInt(b.dataset.s)<=n)?'#f59e0b':'#9ca3af');
              stars.forEach(b=>{
                b.addEventListener('mouseover',()=>paint(parseInt(b.dataset.s)));
                b.addEventListener('mouseleave',()=>paint(rating));
                b.addEventListener('click',()=>{ rating=parseInt(b.dataset.s); paint(rating); });
              });
            },
            preConfirm: async () => {
              if (rating < 1) { Swal.showValidationMessage('Selecciona una calificación.'); return false; }
              const fd = new FormData();
              fd.append('_token', csrf);
              fd.append('rating', rating);
              fd.append('comment', Swal.getHtmlContainer().querySelector('#c').value || '');
              const res = await fetch(storeUrl, { method:'POST', body: fd, headers:{'Accept':'application/json'} });
              if (!res.ok) { Swal.showValidationMessage('No se pudo guardar la calificación.'); return false; }
              return true;
            }
          }).then(r=>{
            if (r.isConfirmed) {
              Swal.fire({icon:'success',title:'¡Gracias!', timer:1200, showConfirmButton:false})
                .then(()=>forceLogout());
            } else {
              // Canceló calificación → preguntar si desea salir igual
              Swal.fire({
                title:'¿Salir sin calificar?', icon:'warning',
                showCancelButton:true, confirmButtonText:'Sí, salir', cancelButtonText:'Volver'
              }).then(rr=>{ if (rr.isConfirmed) forceLogout(); });
            }
          });
        }
      });
    </script>
  @endpush
@endsection
