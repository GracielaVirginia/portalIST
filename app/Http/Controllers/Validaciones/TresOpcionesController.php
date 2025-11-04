<?php

namespace App\Http\Controllers\Validaciones;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use App\Models\GestionSaludCompleta;
use Carbon\Carbon;

class TresOpcionesController extends Controller
{
    /**
     * Máximo de intentos antes de bloquear.
     */
    private int $maxIntentos = 3;

    /**
     * Destino en éxito (home del portal).
     */
    private function destinoExito(): string
    {
        // Pediste redirigir al home del portal
        if (Route::has('portal.home')) return 'portal.home';
        if (Route::has('ver-resultados')) return 'ver-resultados';
        return 'portal.home';
    }

    /**
     * GET /validacion/tres-opciones
     */
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'La sesión no se mantiene.');
        }

        $user = Auth::user();

        if (!empty($user->is_blocked)) {
            return redirect()->route('login')->with('error_message', 'Tu cuenta está bloqueada. Comunícate con el administrador.');
        }

        // Inicializa intentos si viene null
        if ($user->failed_validated_attempts === null) {
            $user->failed_validated_attempts = $this->maxIntentos;
            $user->save();
        }

        $rut = $user->username ?? $user->rut ?? $user->numero_documento ?? null;

        $gestiones = GestionSaludCompleta::query()
            ->when($rut, fn($q) => $q->where('numero_documento', $rut))
            ->orderByDesc('created_at')
            ->get();

        $g = $gestiones->first();

        // --- Teléfono ---
        $celularRaw  = $g->telefono ?? $g->celular ?? $user->celular ?? '';
        $telefono    = $this->soloDigitos($celularRaw);
        $telefonoOk  = $this->esCelularCl($telefono);
        $telefonoMask= $telefonoOk ? $this->maskTelefono($telefono) : null;

        // --- Email ---
        $emailRaw    = $g->email ?? $user->email ?? '';
        [$emailOk, $emailMask] = $this->maskEmail($emailRaw);

        // --- Prestaciones (únicas) ---
        [$prestaciones, $codigos] = $this->extraerPrestaciones($gestiones);

        // Para depuración en la vista (si quieres renderizar lo que el paciente verá como opciones)
        session()->flash('debug_validacion_index', [
            'telefono_real'      => $telefonoOk ? $telefono : null,
            'telefono_mask'      => $telefonoMask,
            'email_real'         => $emailOk ? $emailRaw : null,
            'email_mask'         => $emailMask,
            'examenes_codigos'   => $codigos,
            'intentos_restantes' => (int) $user->failed_validated_attempts,
        ]);

        return view('validaciones.tres-opciones', [
            'user'                  => $user,
            'telefono'              => $telefonoOk ? $telefono : null,
            'celular'               => $celularRaw,
            'telefono_enmascarado'  => $telefonoMask,
            'telefono_valido'       => $telefonoOk,
            'email'                 => $emailOk ? $emailRaw : null,
            'email_enmascarado'     => $emailMask,
            'prestaciones'          => $prestaciones,
            'intentos_restantes'    => (int) $user->failed_validated_attempts,
        ]);
    }

    /**
     * POST /verificar-usuario
     */
    public function verificarUsuario(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Sesión expirada.');
        }

        $user = Auth::user();

        if (!empty($user->is_blocked)) {
            return redirect()->route('login')->with('error_message', 'Tu cuenta está bloqueada. Comunícate con el administrador.');
        }

        // Inicializa intentos si viene null
        if ($user->failed_validated_attempts === null) {
            $user->failed_validated_attempts = $this->maxIntentos;
            $user->save();
        }

        $rut = $user->username ?? $user->rut ?? $user->numero_documento ?? null;

        $data = $request->validate([
            'tipo_validacion' => 'required|in:telefono,email,examen',
            'valor'           => 'nullable|string',
            'examen_id'       => 'nullable|string',
        ]);

        $tipo      = $data['tipo_validacion'];
        $valor     = $data['valor'] ?? null;
        $examen_id = $data['examen_id'] ?? null;

        // Cargamos gestiones
        $gestiones = GestionSaludCompleta::query()
            ->when($rut, fn($q) => $q->where('numero_documento', $rut))
            ->orderByDesc('created_at')
            ->get();

        $g = $gestiones->first();

        // Calculamos opciones actuales (para log y vista si falla)
        $celularRaw   = $g->telefono ?? $g->celular ?? $user->celular ?? '';
        $telefono     = $this->soloDigitos($celularRaw);
        $telOk        = $this->esCelularCl($telefono);
        $telMask      = $telOk ? $this->maskTelefono($telefono) : null;

        $emailRaw     = $g->email ?? $user->email ?? '';
        [$emailOk, $emailMask] = $this->maskEmail($emailRaw);

        [$prestaciones, $codigos] = $this->extraerPrestaciones($gestiones);

        // --- Validación real (acepta valor real o máscara que muestres en UI) ---
        $validado = false;

        if ($tipo === 'telefono') {
            if ($telOk) {
                // Acepta que el front envíe el teléfono real (9 dígitos) o la máscara
                $matchReal  = ($valor !== null) && hash_equals($telefono, preg_replace('/\D/u', '', $valor));
                $matchMask  = ($valor !== null) && hash_equals($telMask, $valor);
                $validado = $matchReal || $matchMask;
            }
        } elseif ($tipo === 'email') {
            if ($emailOk) {
                // Acepta email real (case-insensitive) o máscara
                $matchReal = ($valor !== null) && strcasecmp(trim($valor), trim($emailRaw)) === 0;
                $matchMask = ($valor !== null) && hash_equals($emailMask, $valor);
                $validado = $matchReal || $matchMask;
            }
        } elseif ($tipo === 'examen') {
            if (!empty($examen_id)) {
                $validado = in_array((string) $examen_id, $codigos, true);
            }
        }

        // ==== TRAZABILIDAD (SIEMPRE) ====
        Log::info('[validacion.intentada]', [
            'user_id'            => $user->id,
            'tipo'               => $tipo,
            'valor_enviado'      => $valor,
            'examen_id_enviado'  => $examen_id,
            'opciones' => [
                'telefono_real'  => $telOk ? $telefono : null,
                'telefono_mask'  => $telMask,
                'email_real'     => $emailOk ? $emailRaw : null,
                'email_mask'     => $emailMask,
                'examenes'       => $codigos,
            ],
            'resultado'          => $validado ? 'OK' : 'FALLA',
            'intentos_antes'     => (int) $user->failed_validated_attempts,
        ]);

        if ($validado) {
            // Éxito: marcar validado y resetear intentos
            $user->is_validated             = 1;
            $user->failed_validated_attempts = $this->maxIntentos;
            $user->save();

            return redirect()->route($this->destinoExito())
                ->with('success', 'Validación exitosa. Redirigiendo…');
        }

        // FALLO: descontar 1 intento y decidir bloqueo
        $user->failed_validated_attempts = max(0, (int) $user->failed_validated_attempts - 1);
        $user->save();

        $intentosRestantes = (int) $user->failed_validated_attempts;

        // Guardamos detalles para mostrarlos en la vista de error
        session()->flash('debug_validacion', [
            'tipo_elegido'       => $tipo,
            'valor_enviado'      => $valor,
            'examen_id_enviado'  => $examen_id,
            'opciones_disponibles' => [
                'telefono_real'  => $telOk ? $telefono : null,
                'telefono_mask'  => $telMask,
                'email_real'     => $emailOk ? $emailRaw : null,
                'email_mask'     => $emailMask,
                'examenes'       => $codigos,
            ],
            'intentos_restantes' => $intentosRestantes,
        ]);

        Log::warning('[validacion.fallo]', [
            'user_id'            => $user->id,
            'tipo'               => $tipo,
            'valor_enviado'      => $valor,
            'examen_id_enviado'  => $examen_id,
            'intentos_restantes' => $intentosRestantes,
        ]);

        if ($intentosRestantes <= 0) {
            $this->bloquearUsuario($user);
            return redirect()->route('login')
                ->with('error_message', 'Tu cuenta ha sido bloqueada. Comunícate con el administrador.');
        }

        // Volver con error y con las opciones reconstruidas
        return back()
            ->withErrors([
                'validacion' => 'Datos incorrectos. Te quedan '.$intentosRestantes.' intento(s) antes de ser bloqueado.'
            ])
            ->withInput()
            ->with([
                'user'                  => $user,
                'telefono'              => $telOk ? $telefono : null,
                'celular'               => $celularRaw,
                'telefono_enmascarado'  => $telMask,
                'telefono_valido'       => $telOk,
                'email'                 => $emailOk ? $emailRaw : null,
                'email_enmascarado'     => $emailMask,
                'prestaciones'          => $prestaciones,
                'intentos_restantes'    => $intentosRestantes,
            ]);
    }

    /* ===========================
     * Helpers
     * ===========================
     */

    private function soloDigitos(?string $v): string
    {
        return trim(preg_replace('/\D/u', '', (string) ($v ?? '')));
    }

    /**
     * Celular CL de 9 dígitos (sin +56).
     */
    private function esCelularCl(string $tel): bool
    {
        return (strlen($tel) === 9) && (bool) preg_match('/^\d{9}$/', $tel);
    }

    private function maskTelefono(string $tel): string
    {
        // 9 dígitos => 1er dígito + 7 x + último dígito
        return substr($tel, 0, 1) . str_repeat('x', 7) . substr($tel, -1);
    }

    /**
     * Devuelve [bool $valido, string|null $mask]
     */
    private function maskEmail(?string $email): array
    {
        $email = (string) ($email ?? '');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return [false, null];
        }
        [$local, $domain] = explode('@', $email, 2);
        $mask = substr($local, 0, 2) . str_repeat('*', max(strlen($local) - 2, 0)) . '@' . $domain;
        return [true, $mask];
    }

    /**
     * Construye lista única de prestaciones desde gestiones.
     * Retorna [array $prestacionesList, array $codigosList]
     */
    private function extraerPrestaciones($gestiones): array
    {
        $examCodeCandidates = ['cod_prestacion', 'codigo_examen', 'examen_codigo', 'codigo', 'codigo_prestacion'];
        $examNameCandidates = ['nombre_prestacion', 'examen_nombre', 'prestacion', 'procedimiento', 'nombre'];

        $pick = function ($row, array $cands) {
            foreach ($cands as $c) {
                if (isset($row->{$c}) && $row->{$c} !== null && $row->{$c} !== '') {
                    return (string) $row->{$c};
                }
            }
            return null;
        };

        $prestaciones = [];
        foreach ($gestiones as $row) {
            $codeVal = $pick($row, $examCodeCandidates);
            $nameVal = $pick($row, $examNameCandidates);
            if ($codeVal) {
                $prestaciones[$codeVal] = (object) [
                    'codigo' => $codeVal,
                    'nombre' => $nameVal ?: $codeVal,
                ];
            }
        }

        $list  = array_values($prestaciones);
        $codes = array_map(fn($o) => (string) $o->codigo, $list);

        return [$list, $codes];
    }

    /**
     * Bloquea al usuario y setea campos relacionados.
     */
    private function bloquearUsuario($user): void
    {
        $user->is_blocked = 1;
        if (isset($user->blocked_at))            { $user->blocked_at = Carbon::now(); }
        if (isset($user->failed_login_attempts)) { $user->failed_login_attempts = 0; }
        if (isset($user->password_needs_change)) { $user->password_needs_change = 1; }
        if (isset($user->is_validated))          { $user->is_validated = 0; }
        $user->save();

        Log::warning('[validacion.bloqueado]', ['user_id' => $user->id]);
    }
}
