<?php

namespace App\Http\Controllers;

use App\Models\Horario;
use App\Models\Profesional;
use App\Models\Sucursal;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class HorarioController extends Controller
{
    public function index()
    {
        $horarios = Horario::with(['profesional.tipoProfesional','sucursal'])
            ->orderBy('idsucursal')
            ->orderBy('profesional_id')
            ->orderByRaw("FIELD(dia_semana,'lunes','martes','miércoles','miercoles','jueves','viernes','sábado','sabado','domingo')")
            ->orderBy('hora_inicio')
            ->get();

        return view('admin.horarios.index', compact('horarios'));
    }

    public function create()
    {
        $sucursales   = Sucursal::orderBy('orden')->orderBy('nombre')->get(['id','nombre']);
        $profesionales= Profesional::with('sucursal','tipoProfesional')
                            ->orderBy('idsucursal')->orderBy('nombres')->get();
        $horario = new Horario();

        return view('admin.horarios.create', compact('sucursales','profesionales','horario'));
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        // Forzar coherencia sucursal-profesional
        $prof = Profesional::findOrFail($data['profesional_id']);
        $data['idsucursal'] = $prof->idsucursal;
        $data['idempresa']  = 1;

        // Reglas de negocio: no solapar con otros horarios del mismo profesional en el mismo día
        if ($this->haySolape($data, null)) {
            return back()->withErrors(['hora_inicio' => 'La franja se solapa con otro horario del mismo día.'])
                         ->withInput();
        }

        Horario::create($data);

        return redirect()->route('horarios.index')->with('success', 'Horario creado.');
    }

    public function edit(Horario $horario)
    {
        $sucursales   = Sucursal::orderBy('orden')->orderBy('nombre')->get(['id','nombre']);
        $profesionales= Profesional::with('sucursal','tipoProfesional')
                            ->orderBy('idsucursal')->orderBy('nombres')->get();

        return view('admin.horarios.edit', compact('sucursales','profesionales','horario'));
    }

    public function update(Request $request, Horario $horario)
    {
        $data = $this->validateData($request);

        $prof = Profesional::findOrFail($data['profesional_id']);
        $data['idsucursal'] = $prof->idsucursal;

        if ($this->haySolape($data, $horario->id)) {
            return back()->withErrors(['hora_inicio' => 'La franja se solapa con otro horario del mismo día.'])
                         ->withInput();
        }

        $horario->update($data);

        return redirect()->route('horarios.index')->with('success', 'Horario actualizado.');
    }

    public function destroy(Horario $horario)
    {
        $horario->delete();
        return redirect()->route('horarios.index')->with('success', 'Horario eliminado.');
    }

    // -------- Helpers --------

    private function validateData(Request $request): array
    {
        $dias = ['lunes','martes','miércoles','miercoles','jueves','viernes','sábado','sabado','domingo'];

        return $request->validate([
            'profesional_id'  => ['required','exists:profesionales,id'],
            'dia_semana'      => ['required','string', Rule::in($dias)],
            'hora_inicio'     => ['required','date_format:H:i'],
            'hora_fin'        => ['required','date_format:H:i','after:hora_inicio'],
            'duracion_bloque' => ['required','integer','min:5','max:180'],
            'tipo'            => ['nullable','string','max:50'],
        ],[
            'dia_semana.in'   => 'Día inválido.',
            'hora_fin.after'  => 'La hora fin debe ser posterior a la hora inicio.',
        ]);
    }

    private function haySolape(array $data, ?int $ignorarId = null): bool
    {
        // Busca horarios del mismo profesional y día que se crucen con [inicio, fin)
        $q = Horario::where('profesional_id', $data['profesional_id'])
            ->where(function($qq) use ($data) {
                $qq->where('dia_semana', $data['dia_semana'])
                   ->orWhere(function($alt) use ($data) {
                       // normalizamos miercoles/miércoles; sabado/sábado
                       $map = ['miercoles'=>'miércoles','sabado'=>'sábado'];
                       $dia = mb_strtolower($data['dia_semana']);
                       $norm = $map[$dia] ?? $dia;
                       $alt->where('dia_semana', $norm);
                   });
            })
            ->where(function($r) use ($data) {
                // solape cuando: inicio < otro.fin && fin > otro.inicio
                $r->where('hora_inicio', '<', $data['hora_fin'])
                  ->where('hora_fin', '>', $data['hora_inicio']);
            });

        if ($ignorarId) $q->where('id','!=',$ignorarId);

        return $q->exists();
    }
}
