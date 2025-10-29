<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PortalValidacionConfig;

class ValidacionController extends Controller
{
    /**
     * Mostrar la vista con las formas de validación
     * para que el administrador seleccione una.
     */
    public function index()
    {
        // Obtener todas las opciones de validación (ordenadas por id)
        $opciones = PortalValidacionConfig::orderBy('id')->get();

        // Retornar la vista con las opciones
        return view('admin.validacion.modos', compact('opciones'));
    }

    /**
     * Guardar la selección del modo de validación activo.
     */
    public function guardar(Request $request)
    {
        // Validar que se haya enviado un id válido
        $request->validate([
            'id' => 'required|exists:portal_validacion_config,id',
        ]);

        // Desactivar todos los modos de validación
        PortalValidacionConfig::query()->update(['activo' => false]);

        // Activar el modo seleccionado
        PortalValidacionConfig::where('id', $request->id)->update(['activo' => true]);

        // Redirigir de nuevo con mensaje de éxito
        return redirect()
            ->route('admin.validacion.modos')
            ->with('ok', 'Modo de validación actualizado correctamente.');
    }
}
