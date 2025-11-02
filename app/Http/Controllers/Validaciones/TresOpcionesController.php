<?php

namespace App\Http\Controllers\Validaciones;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\GestionSaludCompleta;

class TresOpcionesController extends Controller
{
    /**
     * GET /validacion/tres-opciones
     * Muestra la vista con las 3 opciones de validación.
     * Toma teléfono, email y la lista de exámenes desde gestiones_salud_completa.
     */
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'La sesión no se mantiene.');
        }

        $user = Auth::user();
        $rut  = $user->username ?? $user->rut ?? $user->numero_documento ?? null;

        // Traer todas las gestiones del paciente para armar opciones de examen
        $gestiones = GestionSaludCompleta::query()
            ->when($rut, fn($q) => $q->where('numero_documento', $rut))
            ->orderByDesc('created_at')
            ->get();

        // Tomar la gestión más reciente para teléfono/email
        $g = $gestiones->first();

        // --- Teléfono (desde gestiones) ---
        $celularRaw = $g->telefono ?? $g->celular ?? $user->celular ?? '';
        $telefono   = preg_replace('/\D/u', '', (string) $celularRaw);
        $telefono   = trim($telefono);
        $telefono_valido = (strlen($telefono) === 9) && preg_match('/^\d{9}$/', $telefono);
        $telefono_enmascarado = $telefono_valido
            ? substr($telefono, 0, 1) . str_repeat('x', 7) . substr($telefono, -1)
            : null;

        // --- Email (desde gestiones, si no está, desde user) ---
        $email = $g->email ?? $user->email ?? '';
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            [$local, $domain] = explode('@', $email, 2);
            $email_enmascarado = substr($local, 0, 2) . str_repeat('*', max(strlen($local) - 2, 0)) . '@' . $domain;
        } else {
            $email_enmascarado = null;
        }

        // --- Lista de exámenes (código + nombre) desde gestiones ---
        // Intentamos detectar columnas típicas para código/nombre de examen.
        $examCodeCandidates = ['cod_prestacion', 'codigo_examen', 'examen_codigo', 'codigo', 'codigo_prestacion'];
        $examNameCandidates = ['nombre_prestacion', 'examen_nombre', 'prestacion', 'procedimiento', 'nombre'];

        $pick = function ($row, array $cands) {
            foreach ($cands as $c) {
                if (isset($row->{$c}) && $row->{$c} !== null && $row->{$c} !== '') {
                    return [$c, $row->{$c}];
                }
            }
            return [null, null];
        };

        $prestaciones = [];
        foreach ($gestiones as $row) {
            [$codeCol, $codeVal] = $pick($row, $examCodeCandidates);
            [$nameCol, $nameVal] = $pick($row, $examNameCandidates);
            if ($codeVal) {
                $prestaciones[$codeVal] = (object) [
                    'codigo' => $codeVal,
                    'nombre' => $nameVal ?: $codeVal,
                ];
            }
        }
        // Reindexar a lista
        $prestaciones = array_values($prestaciones);

        return view('validaciones.tres-opciones', [
            'user'                  => $user,
            'telefono'              => $telefono_valido ? $telefono : null,
            'celular'               => $celularRaw,
            'telefono_enmascarado'  => $telefono_enmascarado,
            'telefono_valido'       => $telefono_valido,
            'email'                 => $email,
            'email_enmascarado'     => $email_enmascarado,
            'prestaciones'          => $prestaciones,
        ]);
    }

    /**
     * POST /verificar-usuario
     * Procesa la validación: teléfono | email | examen
     * Compara contra los datos de gestiones_salud_completa.
     */
    public function verificarUsuario(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Sesión expirada.');
        }

        $user = Auth::user();
        $rut  = $user->username ?? $user->rut ?? $user->numero_documento ?? null;

        $data = $request->validate([
            'tipo_validacion' => 'required|in:telefono,email,examen',
            'valor'           => 'nullable|string',
            'examen_id'       => 'nullable|string',
        ]);

        $tipo      = $data['tipo_validacion'];
        $valor     = $data['valor'] ?? null;
        $examen_id = $data['examen_id'] ?? null;

        Log::info('[validacion] tipo='.$tipo.' valor=' . ($valor ?? 'null') . ' examen_id=' . ($examen_id ?? 'null'));

        // Control de intentos
        if (($user->intentos_validacion ?? 0) <= 0) {
            $user->bloqueado_validacion = true;
            $user->save();
            return redirect()->route('login')->with([
                'error_message'  => 'Tu cuenta ha sido bloqueada. Comunícate con el administrador.',
            ]);
        }

        // Cargar últimas gestiones del paciente
        $gestiones = GestionSaludCompleta::query()
            ->when($rut, fn($q) => $q->where('numero_documento', $rut))
            ->orderByDesc('created_at')
            ->get();

        $g = $gestiones->first();
        $validado = false;

        switch ($tipo) {
            case 'telefono':
                $celularRaw = $g->telefono ?? $g->celular ?? $user->celular ?? '';
                $tel = preg_replace('/\D/u', '', (string) $celularRaw);
                $tel = trim($tel);
                $ok  = (strlen($tel) === 9) && preg_match('/^\d{9}$/', $tel);
                $mask = $ok ? substr($tel, 0, 1) . str_repeat('x', 7) . substr($tel, -1) : null;
                if ($mask && $valor && hash_equals($mask, $valor)) {
                    $validado = true;
                }
                break;

            case 'email':
                $email = $g->email ?? $user->email ?? '';
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    [$local, $domain] = explode('@', $email, 2);
                    $mask = substr($local, 0, 2) . str_repeat('*', max(strlen($local) - 2, 0)) . '@' . $domain;
                    if ($valor && hash_equals($mask, $valor)) {
                        $validado = true;
                    }
                }
                break;

            case 'examen':
                if (!empty($examen_id)) {
                    // Detectar columnas posibles de "código de examen" en gestiones
                    $examCodeCandidates = ['cod_prestacion', 'codigo_examen', 'examen_codigo', 'codigo', 'codigo_prestacion'];

                    $existe = $gestiones->contains(function ($row) use ($examen_id, $examCodeCandidates) {
                        foreach ($examCodeCandidates as $c) {
                            if (isset($row->{$c}) && (string)$row->{$c} !== '') {
                                if ((string)$row->{$c} === (string)$examen_id) {
                                    return true;
                                }
                            }
                        }
                        return false;
                    });

                    if ($existe) {
                        $validado = true;
                    }
                }
                break;
        }

        if ($validado) {
            $user->validado = true;
            $user->save();
            return redirect()->route('ver-resultados')->with('success', 'Validación exitosa. Redirigiendo…');
        }

        // Fallo: descontar intento
        $user->intentos_validacion = max(0, (int) $user->intentos_validacion - 1);
        $user->save();

        if (($user->intentos_validacion ?? 0) <= 0) {
            $user->bloqueado_validacion = true;
            $user->save();
            return redirect()->route('login')->with([
                'error_message'  => 'Tu cuenta ha sido bloqueada. Comunícate con el administrador.',
            ]);
        }

        // Reconstruir datos para re-render con error
        $celularRaw = $g->telefono ?? $g->celular ?? $user->celular ?? '';
        $tel = preg_replace('/\D/u', '', (string) $celularRaw);
        $tel = trim($tel);
        $telOk = (strlen($tel) === 9) && preg_match('/^\d{9}$/', $tel);
        $telMask = $telOk ? substr($tel, 0, 1) . str_repeat('x', 7) . substr($tel, -1) : null;

        $email = $g->email ?? $user->email ?? '';
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            [$local, $domain] = explode('@', $email, 2);
            $emailMask = substr($local, 0, 2) . str_repeat('*', max(strlen($local) - 2, 0)) . '@' . $domain;
        } else {
            $emailMask = null;
        }

        // Reconstruir lista de prestaciones
        $examCodeCandidates = ['cod_prestacion', 'codigo_examen', 'examen_codigo', 'codigo', 'codigo_prestacion'];
        $examNameCandidates = ['nombre_prestacion', 'examen_nombre', 'prestacion', 'procedimiento', 'nombre'];
        $pick = function ($row, array $cands) {
            foreach ($cands as $c) {
                if (isset($row->{$c}) && $row->{$c} !== null && $row->{$c} !== '') {
                    return [$c, $row->{$c}];
                }
            }
            return [null, null];
        };
        $prestaciones = [];
        foreach ($gestiones as $row) {
            [$codeCol, $codeVal] = $pick($row, $examCodeCandidates);
            [$nameCol, $nameVal] = $pick($row, $examNameCandidates);
            if ($codeVal) {
                $prestaciones[$codeVal] = (object) [
                    'codigo' => $codeVal,
                    'nombre' => $nameVal ?: $codeVal,
                ];
            }
        }
        $prestaciones = array_values($prestaciones);

        return back()
            ->withErrors(['validacion' => 'Datos incorrectos, te quedan: ' . $user->intentos_validacion . ' intento(s) antes de ser bloqueado.'])
            ->withInput()
            ->with([
                'user'                  => $user,
                'telefono'              => $telOk ? $tel : null,
                'celular'               => $celularRaw,
                'telefono_enmascarado'  => $telMask,
                'telefono_valido'       => $telOk,
                'email'                 => $email,
                'email_enmascarado'     => $emailMask,
                'prestaciones'          => $prestaciones,
            ]);
    }
}
