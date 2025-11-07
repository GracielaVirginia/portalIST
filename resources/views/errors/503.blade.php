<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Mantenimiento</title>

  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          colors: {
            primary: {
              50: '#f5f3ff',
              100: '#ede9fe',
              200: '#ddd6fe',
              300: '#c4b5fd',
              400: '#a78bfa',
              500: '#8b5cf6',
              600: '#7c3aed',
              700: '#6d28d9',
              800: '#5b21b6',
              900: '#4c1d95',
            }
          }
        }
      }
    }
  </script>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap');
    body {
      font-family: 'Inter', sans-serif;
    }
  </style>
</head>
<body class="bg-purple-50 dark:bg-gray-900 text-purple-900 dark:text-gray-200 min-h-screen flex items-center justify-center p-4">

  <!-- Botón de cambio de tema (discreto) -->
  <button id="themeToggle"
          class="absolute top-4 right-4 p-2 rounded-full
                 bg-white/80 dark:bg-gray-800/80
                 text-purple-900 dark:text-gray-200 shadow-sm">
    <i id="themeIcon" class="text-sm">🌙</i>
  </button>

  <!-- ✨ Tarjeta con bordes y sombra ✨ -->
  <div class="text-center max-w-md w-full space-y-6 p-6 rounded-xl shadow-md
              bg-white/80 dark:bg-gray-800/80 backdrop-blur">

    <!-- Tu icono, sin cambios -->
    <div class="text-4xl">⚙️</div>

    <!-- Texto mejorado -->
    <div>
      <h1 class="text-xl font-semibold">
        Estamos haciendo mejoras
      </h1>
      <p class="mt-2 text-sm leading-relaxed text-purple-800 dark:text-gray-300">
        Nuestro equipo está trabajando para ofrecerte una experiencia más rápida y segura. 
        Vuelve en unos minutos. ¡Gracias por tu confianza!
      </p>
    </div>

    <!-- Soporte -->
    <div class="pt-4 border-t border-purple-200/60 dark:border-gray-800">
      <p class="text-xs text-purple-700 dark:text-gray-400 mb-1">
        ¿Algo urgente?
      </p>
      <a href="mailto:soporte@tuappsalud.cl"
         class="text-sm font-medium text-purple-600 hover:text-purple-800 
                dark:text-purple-400 dark:hover:text-purple-300">
        soporte@tuappsalud.cl
      </a>
    </div>

    <!-- Animación de carga -->
    <div class="flex justify-center space-x-1">
      <div class="w-1.5 h-1.5 rounded-full bg-purple-500 animate-bounce"></div>
      <div class="w-1.5 h-1.5 rounded-full bg-purple-500 animate-bounce" style="animation-delay:0.2s"></div>
      <div class="w-1.5 h-1.5 rounded-full bg-purple-500 animate-bounce" style="animation-delay:0.4s"></div>
    </div>

  </div>

  <script>
    const html = document.documentElement;
    const toggle = document.getElementById('themeToggle');
    const icon = document.getElementById('themeIcon');

    const saved = localStorage.getItem('theme') || 
                  (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
    
    if (saved === 'dark') {
      html.classList.add('dark');
      icon.textContent = '☀️';
    } else {
      html.classList.remove('dark');
      icon.textContent = '🌙';
    }

    toggle.addEventListener('click', () => {
      const isDark = html.classList.toggle('dark');
      localStorage.setItem('theme', isDark ? 'dark' : 'light');
      icon.textContent = isDark ? '☀️' : '🌙';
    });
  </script>
</body>
</html>