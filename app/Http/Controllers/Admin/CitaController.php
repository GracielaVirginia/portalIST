<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cita;
use App\Models\Profesional;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CitaController extends Controller
{
    /** Lista para DataTable */
    public function index()
    {
        $citas = Cita::with(['profesional', 'paciente'])
            ->orderBy('fecha', 'desc')
            ->orderBy('hora_inicio', 'asc')
            ->get();

        return view('admin.citas.index', compact('citas'));
    }

    /** Editar */
    public function edit(Cita $cita)
    {
        $profesionales = Profesional::orderBy('apellidos')
            ->orderBy('nombres')
            ->get(['id','nombres','apellidos']);

        if (method_exists($cita, 'getAttribute') && !is_string($cita->fecha)) {
            $cita->fecha = optional($cita->fecha)->format('Y-m-d') ?? $cita->fecha;
        }

        return view('admin.citas.edit', compact('cita','profesionales'));
    }

    /** Update */
    public function update(Request $request, Cita $cita)
    {
        $data = $request->validate([
            'profesional_id' => ['required', Rule::exists('profesionales', 'id')],
            'fecha'          => ['required', 'date'],
            'hora_inicio'    => ['required', 'date_format:H:i'],
            'hora_fin'       => ['required', 'date_format:H:i', 'after:hora_inicio'],
            'tipo_atencion'  => ['required', Rule::in(['presencial','remota'])],
            'estado'         => ['required', Rule::in(['reservada','confirmada','cancelada','atendida'])],
            'motivo'         => ['nullable', 'string', 'max:2000'],
        ], [
            'hora_fin.after' => 'La hora de fin debe ser posterior a la hora de inicio.',
        ]);

        // si cambian el profesional, sincroniza idsucursal
        if ((int)$cita->profesional_id !== (int)$data['profesional_id']) {
            $idsucursalNueva = Profesional::where('id', (int)$data['profesional_id'])->value('idsucursal');
            if ($idsucursalNueva) {
                $cita->idsucursal = $idsucursalNueva;
            }
        }

        $cita->update($data);

        return redirect()->route('admin.citas.index')->with('ok', 'Cita actualizada.');
    }

    /** Destroy */
    public function destroy(Cita $cita)
    {
        $cita->delete();
        return back()->with('ok', 'Cita eliminada.');
    }

    /** ====== ACCIONES DESDE BADGE (AJAX) ====== */

    /** PUT/PATCH: marcar como CONFIRMADA */
    public function confirmar(Request $request, Cita $cita)
    {
        if ($cita->estado !== 'confirmada') {
            $cita->estado = 'confirmada';
            $cita->save();
        }
        return $this->respondEstado($request, $cita, 'Cita confirmada.');
    }

    /** PUT/PATCH: devolver a RESERVADA */
    public function reservada(Request $request, Cita $cita)
    {
        if ($cita->estado !== 'reservada') {
            $cita->estado = 'reservada';
            $cita->save();
        }
        return $this->respondEstado($request, $cita, 'Cita marcada como reservada.');
    }

    /** Helper: responde JSON si viene por fetch, o redirige con flash si no */
    private function respondEstado(Request $request, Cita $cita, string $flash)
    {
        if ($request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'ok'     => true,
                'id'     => $cita->id,
                'estado' => $cita->estado,
            ]);
        }
        return back()->with('ok', $flash);
    }
}
