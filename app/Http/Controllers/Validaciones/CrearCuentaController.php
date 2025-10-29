<?php

namespace App\Http\Controllers\Validaciones;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\CodigoVerificacionMail;
use App\Models\GestionSaludCompleta;
use App\Models\EmailVerificationRequest;

class CrearCuentaController extends Controller
{
    public function index()
    {
        return view('validaciones.crear-cuenta');
    }

    /**
     * POST /validacion/cuenta/codigo
     * Ruta: validacion.cuenta.codigo
     * Envía el código al email y registra todo en la tabla + logs.
     */
    public function enviarCodigo(Request $request)
    {
        $data = $request->validate([
            'email' => ['required','email','max:150'],
        ]);

        $email = strtolower(trim($data['email']));

        // Throttle: evita spam (IP+email) -> 1 cada 30s
        $throttleKey = 'vcc:throttle:'.sha1($request->ip().'|'.$email);
        if (Cache::has($throttleKey)) {
            Log::warning('[validacion.codigo] Throttle hit', [
                'email' => $email,
                'ip'    => $request->ip(),
            ]);
            return response()->json([
                'ok'      => false,
                'message' => 'Espera unos segundos antes de solicitar otro código.'
            ], 429);
        }
        Cache::put($throttleKey, true, now()->addSeconds(30));

        // Generar código 6 dígitos (TTL 10 min)
        $code = (string) random_int(100000, 999999);
        $codeKey = 'vcc:code:'.sha1($email);
        Cache::put($codeKey, $code, now()->addMinutes(10));

        // Registrar solicitud en tabla (estado pending)
        $req = EmailVerificationRequest::create([
            'email'      => $email,
            'code'       => $code,
            'status'     => 'pending',
            'attempts'   => 0,
            'ip'         => $request->ip(),
            'user_agent' => substr($request->userAgent() ?? '', 0, 255),
        ]);

        // Intentar envío
        try {
            $req->attempts = $req->attempts + 1;
            Mail::to($email)->send(new CodigoVerificacionMail($code));

            // Si no lanzó excepción, lo damos por enviado
            $req->status  = 'sent';
            $req->sent_at = now();
            $req->last_error = null;
            $req->save();

            Log::info('[validacion.codigo] Código enviado', [
                'id'    => $req->id,
                'email' => $email,
                'ip'    => $request->ip(),
                'port'  => config('mail.mailers.smtp.port'),
                'host'  => config('mail.mailers.smtp.host'),
            ]);

            return response()->json([
                'ok'      => true,
                'message' => 'Código enviado a tu correo.'
            ]);
        } catch (\Throwable $e) {

            $req->status = 'failed';
            $req->last_error = $e->getMessage();
            $req->save();

            Log::error('[validacion.codigo] Error al enviar', [
                'id'        => $req->id,
                'email'     => $email,
                'ip'        => $request->ip(),
                'exception' => get_class($e),
                'message'   => $e->getMessage(),
            ]);

            return response()->json([
                'ok'      => false,
                'message' => 'No se pudo enviar el código. Inténtalo nuevamente más tarde.'
            ], 500);
        }
    }

    /**
     * POST /validacion/cuenta
     * Ruta: validacion.cuenta.store
     * Verifica código + crea/actualiza usuario (tu lógica original, extendida).
     */
    public function store(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'rut'               => ['required','string','max:12','regex:/^[0-9]+-[0-9Kk]$/'],
            'name'              => ['required','string','max:150'],
            'email'             => ['nullable','email','max:150'],
            'telefono'          => ['nullable','string','max:30'],
            'grupo_sanguineo'   => ['nullable','in:A+,A-,B+,B-,AB+,AB-,O+,O-'],
            'sexo'              => ['nullable','in:F,M'],
            'fecha_nacimiento'  => ['nullable','date'],
            'alergias'          => ['nullable','string','max:2000'],
            'password'          => ['required','string','min:8','regex:/[A-Z]/','regex:/[0-9]/','confirmed'],
            'accept_terms'      => ['accepted'],

            // Código que llega desde la vista
            'verification_code' => ['required','digits:6'],
        ], [
            'password.regex'    => 'La contraseña debe incluir al menos una mayúscula y un número.',
            'accept_terms.accepted' => 'Debes aceptar los Términos y la Política de privacidad.',
        ]);

        // Validación del código: cache + tabla
        $email = strtolower(trim($data['email'] ?? ''));
        if (!$email) {
            return back()->withErrors([
                'email' => 'Debes ingresar un email para validar el código.',
            ])->withInput();
        }

        $code   = $data['verification_code'];
        $codeKey = 'vcc:code:'.sha1($email);
        $codeSaved = Cache::get($codeKey);

        // Última solicitud válida en 10 minutos con status sent y mismo código
        $req = EmailVerificationRequest::where('email', $email)
                ->where('code', $code)
                ->where('status', 'sent')
                ->where('created_at', '>=', now()->subMinutes(10))
                ->latest('id')
                ->first();

        if (!$codeSaved || $codeSaved !== $code || !$req) {
            Log::warning('[validacion.store] Código inválido/expirado', [
                'email' => $email,
                'code'  => $code,
                'has_cache' => (bool)$codeSaved,
                'has_row'   => (bool)$req,
            ]);

            return back()
                ->withErrors(['verification_code' => 'El código es inválido o expiró.'])
                ->withInput();
        }

        // Consumir código: limpiar cache y marcar verificado en tabla
        Cache::forget($codeKey);
        $req->status = 'verified';
        $req->verified_at = now();
        $req->save();

        Log::info('[validacion.store] Código verificado', [
            'email' => $email,
            'id'    => $req->id,
        ]);

        // ---------- Tu lógica original de persistencia en users ----------
        $user->rut  = strtoupper($data['rut']);
        $user->name = $data['name'];
        if (!empty($data['email'])) {
            $user->email = $data['email'];
        }

        // Guardar opcionalmente teléfono si existe la columna en users
        if (Schema::hasColumn('users', 'telefono') && !empty($data['telefono'])) {
            $user->telefono = $data['telefono'];
        }

        // Campos clínicos opcionales en users si existen columnas
        if (Schema::hasColumn('users', 'grupo_sanguineo') && !empty($data['grupo_sanguineo'])) {
            $user->grupo_sanguineo = $data['grupo_sanguineo'];
        }
        if (Schema::hasColumn('users', 'sexo') && !empty($data['sexo'])) {
            $user->sexo = $data['sexo'];
        }
        if (Schema::hasColumn('users', 'fecha_nacimiento') && !empty($data['fecha_nacimiento'])) {
            $user->fecha_nacimiento = $data['fecha_nacimiento'];
        }
        if (Schema::hasColumn('users', 'alergias') && !empty($data['alergias'])) {
            $user->alergias = $data['alergias'];
        }

        // Password
        $user->password = Hash::make($data['password']);
        if (Schema::hasColumn('users', 'password_needs_change')) {
            $user->password_needs_change = false;
        }
        // Marcar validado
        if (Schema::hasColumn('users', 'is_validated')) {
            $user->is_validated = true;
        }

        $user->save();

        // (Opcional) sincroniza con gestiones_salud_completa si existen columnas
        if (!empty($data['grupo_sanguineo']) || !empty($data['alergias'])) {
            $q = GestionSaludCompleta::query()
                ->where('tipo_documento', 'RUT')
                ->where('numero_documento', strtoupper($data['rut']));

            $update = [];
            if (Schema::hasColumn('gestiones_salud_completa', 'grupo_sanguineo') && !empty($data['grupo_sanguineo'])) {
                $update['grupo_sanguineo'] = $data['grupo_sanguineo'];
            }
            if (Schema::hasColumn('gestiones_salud_completa', 'alergias') && !empty($data['alergias'])) {
                $update['alergias'] = $data['alergias'];
            }
            if (!empty($update)) {
                $q->update($update);
            }
        }

        return redirect()->route('portal.home')->with('success', 'Cuenta creada y validación completada. ¡Bienvenido!');
    }
}
