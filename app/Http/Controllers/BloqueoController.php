<?php

namespace App\Http\Controllers;

use App\Models\Bloqueo;
use App\Models\Horario;
use App\Models\Profesional;
use App\Models\Sucursal;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BloqueoController extends Controller
{
    public function index()
    {
        $bloqueos = Bloqueo::with(['profesional.tipoProfesional','sucursal','horario'])
            ->orderBy('idsucursal')
            ->orderBy('profesional_id')
            ->latest('fecha')
            ->get();

        return view('admin.bloqueos.index', compact('bloqueos'));
    }

    public function create()
    {
        $sucursales    = Sucursal::orderBy('orden')->orderBy('nombre')->get(['id','nombre']);
        $profesionales = Profesional::with(['sucursal','tipoProfesional','horarios'])
                            ->orderBy('idsucursal')->orderBy('nombres')->get();
        $bloqueo = new Bloqueo();

        return view('admin.bloqueos.create', compact('sucursales','profesionales','bloqueo'));
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);

        $prof = Profesional::with('sucursal')->findOrFail($data['profesional_id']);
        $data['idsucursal'] = $prof->idsucursal;
        $data['idempresa']  = 1;

        // Si se indica horario_id, validar que sea del mismo profesional
        if (!empty($data['horario_id'])) {
            $h = Horario::findOrFail($data['horario_id']);
            if ($h->profesional_id !== $prof->id) {
                return back()->withErrors(['horario_id' => 'El horario no pertenece al profesional seleccionado.'])
                             ->withInput();
            }
            // (opcional) validar que el bloqueo caiga dentro de la franja del horario
            if (!$this->bloqueoDentroDeHorario($data['inicio'], $data['duracion'], $h->hora_inicio, $h->hora_fin)) {
                return back()->withErrors(['inicio' => 'El bloqueo no cae dentro del horario seleccionado.'])
                             ->withInput();
            }
        }

        // Debe ser por fecha puntual o por día de semana (uno u otro)
        if (empty($data['fecha']) && empty($data['dia_semana'])) {
            return back()->withErrors(['fecha' => 'Indica una fecha o un día de semana.'])->withInput();
        }
        if (!empty($data['fecha']) && !empty($data['dia_semana'])) {
            return back()->withErrors(['fecha' => 'Usa fecha puntual o día de semana, no ambos.'])->withInput();
        }

        Bloqueo::create($data);

        return redirect()->route('bloqueos.index')->with('success', 'Bloqueo creado.');
    }

    public function edit(Bloqueo $bloqueo)
    {
        $sucursales    = Sucursal::orderBy('orden')->orderBy('nombre')->get(['id','nombre']);
        $profesionales = Profesional::with(['sucursal','tipoProfesional','horarios'])
                            ->orderBy('idsucursal')->orderBy('nombres')->get();

        return view('admin.bloqueos.edit', compact('sucursales','profesionales','bloqueo'));
    }

    public function update(Request $request, Bloqueo $bloqueo)
    {
        $data = $this->validatedData($request);

        $prof = Profesional::with('sucursal')->findOrFail($data['profesional_id']);
        $data['idsucursal'] = $prof->idsucursal;

        if (!empty($data['horario_id'])) {
            $h = Horario::findOrFail($data['horario_id']);
            if ($h->profesional_id !== $prof->id) {
                return back()->withErrors(['horario_id' => 'El horario no pertenece al profesional seleccionado.'])
                             ->withInput();
            }
            if (!$this->bloqueoDentroDeHorario($data['inicio'], $data['duracion'], $h->hora_inicio, $h->hora_fin)) {
                return back()->withErrors(['inicio' => 'El bloqueo no cae dentro del horario seleccionado.'])
                             ->withInput();
            }
        }

        if (empty($data['fecha']) && empty($data['dia_semana'])) {
            return back()->withErrors(['fecha' => 'Indica una fecha o un día de semana.'])->withInput();
        }
        if (!empty($data['fecha']) && !empty($data['dia_semana'])) {
            return back()->withErrors(['fecha' => 'Usa fecha puntual o día de semana, no ambos.'])->withInput();
        }

        $bloqueo->update($data);

        return redirect()->route('bloqueos.index')->with('success', 'Bloqueo actualizado.');
    }

    public function destroy(Bloqueo $bloqueo)
    {
        $bloqueo->delete();
        return redirect()->route('bloqueos.index')->with('success', 'Bloqueo eliminado.');
    }

    // -------- Helpers --------

    private function validatedData(Request $request): array
    {
        $dias = ['lunes','martes','miércoles','miercoles','jueves','viernes','sábado','sabado','domingo'];

        return $request->validate([
            'profesional_id' => ['required','exists:profesionales,id'],
            'horario_id'     => ['nullable','exists:horarios,id'],
            'fecha'          => ['nullable','date'],
            'dia_semana'     => ['nullable','string', Rule::in($dias)],
            'inicio'         => ['required','date_format:H:i'],
            'duracion'       => ['required','integer','min:5','max:600'],
            'motivo'         => ['nullable','string','max:120'],
        ]);
    }

    private function bloqueoDentroDeHorario(string $inicio, int $duracion, string $hIni, string $hFin): bool
    {
        $toMin = fn($hhmm) => (int)substr($hhmm,0,2)*60 + (int)substr($hhmm,3,2);
        $bi = $toMin($inicio);
        $bf = $bi + $duracion;
        return $bi >= $toMin($hIni) && $bf <= $toMin($hFin);
    }
}
