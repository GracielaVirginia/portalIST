<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class SettingController extends Controller
{
    // (Opcional) protege estas rutas con policy o middleware
    // public function __construct()
    // {
    //     $this->middleware('can:manage-settings');
    // }

    /**
     * Muestra el formulario de configuración (solo necesitamos el timeout).
     */
    public function edit()
    {
        $current = DB::table('settings')
            ->where('clave', 'session_timeout')
            ->value('valor');

        // Fallback a 20 si no existe
        $currentTimeout = (int) ($current ?? 20);

        return view('admin.settings.edit', [
            'currentTimeout' => $currentTimeout,
            'allowed' => [5, 10, 15, 20], // opciones visibles en el select
        ]);
    }

    /**
     * Guarda el timeout seleccionado por el admin.
     */
    public function update(Request $request)
    {
        // Validar que solo aceptamos 5,10,15,20
        $data = $request->validate([
            'session_timeout' => 'required|in:5,10,15,20',
        ]);

        // Persistir en settings (upsert)
        DB::table('settings')->updateOrInsert(
            ['clave' => 'session_timeout'],
            ['valor' => (string) $data['session_timeout']]
        );

        // Limpiar cache para que AppServiceProvider vuelva a leer el nuevo valor
        Cache::forget('settings.session_timeout');

        return back()->with('success', 'Tiempo de inactividad actualizado a ' . $data['session_timeout'] . ' minutos.');
    }
}
