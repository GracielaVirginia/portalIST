<?php

namespace App\Http\Controllers\Validaciones;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\GestionSaludCompleta; // <-- usa tu modelo

class NumeroCasoController extends Controller
{
    private const MAX_INTENTOS = 3;
    private const BLOQUEO_MINUTOS = 15;

    public function index(Request $request)
    {
        $user = $request->user();
        [$bloqueado, $intentos] = $this->estadoIntentos($user?->id);

        return view('validaciones.numero-caso', [
            'bloqueado' => $bloqueado,
            'intentosFallidos' => $intentos,
        ]);
    }

    public function procesar(Request $request)
    {
        $data = $request->validate([
            'numero_caso' => ['required', 'string', 'max:50'],
        ], [
            'numero_caso.required' => 'Ingresa tu número de caso.',
        ]);

        $user = $request->user();
        [$bloqueado, $intentos] = $this->estadoIntentos($user?->id);

        if ($bloqueado) {
            return back()->with([
                'error_message' => 'Estás temporalmente bloqueado por intentos fallidos. Intenta más tarde.',
            ]);
        }

        $numeroCaso = trim($data['numero_caso']);

        // VALIDACIÓN REAL contra gestiones_salud_completa
        $valido = $this->verificarCasoContraGestiones($user, $numeroCaso);

        if (!$valido) {
            $intentos++;
            Cache::put($this->keyIntentos($user->id), $intentos, now()->addMinutes(self::BLOQUEO_MINUTOS));

            if ($intentos >= self::MAX_INTENTOS) {
                Cache::put($this->keyBloqueo($user->id), true, now()->addMinutes(self::BLOQUEO_MINUTOS));
                return back()->with(['error_message' => 'Se alcanzó el límite de intentos. Intenta de nuevo más tarde.']);
            }

            return back()
                ->withErrors(['validacion' => 'Número de caso no válido para tu RUT.'])
                ->withInput();
        }

        // ÉXITO: marcar validado si el modelo soporta el flag
        if (property_exists($user, 'is_validated') || array_key_exists('is_validated', $user->getAttributes())) {
            $user->is_validated = true;
            $user->save();
        }

        Cache::forget($this->keyIntentos($user->id));
        Cache::forget($this->keyBloqueo($user->id));

        return redirect()->route('portal.home')->with('success', 'Validación completada. ¡Bienvenido!');
    }

    // ---------- Helpers de intentos/bloqueo ----------
    private function estadoIntentos(?int $userId): array
    {
        if (!$userId) return [false, 0];
        $bloqueado = Cache::get($this->keyBloqueo($userId), false);
        $intentos  = (int) Cache::get($this->keyIntentos($userId), 0);
        return [$bloqueado, $intentos];
    }

    private function keyIntentos(int $userId): string
    {
        return "nc_intentos_user_{$userId}";
    }

    private function keyBloqueo(int $userId): string
    {
        return "nc_bloqueado_user_{$userId}";
    }

    // ---------- Lógica de verificación real ----------
    private function verificarCasoContraGestiones($user, string $numeroCaso): bool
    {
        if (!$user?->rut) {
            return false;
        }

        // Normaliza RUT del usuario (sin puntos, guion ni espacios, uppercase)
        $rutNorm = $this->normalizarRut($user->rut);

        // Busca coincidencia de numero_caso Y numero_documento (normalizado) del paciente
        return GestionSaludCompleta::query()
            ->where('numero_caso', $numeroCaso)
            ->where(function ($q) use ($rutNorm) {
                // Compara normalizando en SQL el campo numero_documento
                $q->whereRaw("
                    REPLACE(
                      REPLACE(
                        REPLACE(UPPER(numero_documento), '.', ''),
                      '-', ''),
                    ' ', '') = ?
                ", [$rutNorm]);
            })
            // (Opcional, si usas 'tipo_documento' = 'RUT')
            // ->where('tipo_documento', 'RUT')
            ->exists();
    }

    private function normalizarRut(string $rut): string
    {
        $rut = strtoupper($rut);
        $rut = str_replace(['.', '-', ' '], '', $rut);
        return $rut;
    }
}
