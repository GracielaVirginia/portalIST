<!-- Sidebar -->
<div class="w-64 bg-custom-primary dark:bg-gray-800 text-white min-h-screen p-6 fixed">
    <div class="flex items-center gap-3 mb-8">
        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-10 h-auto">
        <h2 class="text-xl font-bold">Admin Panel</h2>
    </div>
    <ul class="space-y-4">
        <li>
            <a href="{{ route('admin.dashboard') }}" 
               class="flex items-center gap-3 p-3 rounded-lg {{ request()->routeIs('admin.dashboard') ? 'bg-custom-secondary' : 'hover:bg-custom-secondary hover:bg-opacity-50' }} transition">
                📊 Dashboard
            </a>
        </li>
        <li>
            <a href="{{ route('admin.users') }}" 
               class="flex items-center gap-3 p-3 rounded-lg {{ request()->routeIs('admin.users*') ? 'bg-custom-secondary' : 'hover:bg-custom-secondary hover:bg-opacity-50' }} transition">
                👤 Usuarios
            </a>
        </li>
        <li>
            <a href="{{ route('admin.records') }}" 
               class="flex items-center gap-3 p-3 rounded-lg {{ request()->routeIs('admin.records*') ? 'bg-custom-secondary' : 'hover:bg-custom-secondary hover:bg-opacity-50' }} transition">
                📝 Registros
            </a>
        </li>
        <li>
            <a href="{{ route('admin.settings') }}" 
               class="flex items-center gap-3 p-3 rounded-lg {{ request()->routeIs('admin.settings*') ? 'bg-custom-secondary' : 'hover:bg-custom-secondary hover:bg-opacity-50' }} transition">
                ⚙️ Configuración
            </a>
        </li>
        <li>
            <form method="POST" action="{{ route('admin.logout') }}" class="inline">
                @csrf
                <button type="submit" 
                        class="w-full text-left flex items-center gap-3 p-3 rounded-lg hover:bg-red-700 transition text-white">
                    🔐 Cerrar Sesión
                </button>
            </form>
        </li>
    </ul>
</div>