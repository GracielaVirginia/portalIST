<?php

namespace App\Http\Controllers\Assistant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\AssistantRule;

class ChatBotController extends Controller
{
    public function sendMessage(Request $request)
    {
        // ====== DEBUG INICIAL ======
        Log::info('🤖 [ChatBot@sendMessage] hit', [
            'ip'        => $request->ip(),
            'user_id'   => optional($request->user())->id,
            'method'    => $request->method(),
            'ct'        => $request->header('Content-Type'),
            'accept'    => $request->header('Accept'),
            'isAjax'    => $request->ajax(),
            'payload'   => $request->all(),       // para JSON application/json ya viene parseado
            'raw'       => $request->getContent() // por si Content-Type raro
        ]);

        try {
            $msg = trim((string) $request->input('message', ''));

            // Si no llega nada, reporta qué vimos
            if ($msg === '') {
                Log::warning('🤖 [ChatBot@sendMessage] mensaje vacío', [
                    'payload' => $request->all(),
                    'raw'     => $request->getContent(),
                ]);

                return response()->json([
                    'ok'    => true,
                    'reply' => 'Escribe tu consulta, por favor.',
                    'debug' => app()->isLocal() ? ['empty' => true] : null,
                ]);
            }

            $msgLower = mb_strtolower($msg, 'UTF-8');

            // Carga reglas activas ordenadas por prioridad
            $rules = AssistantRule::active()->ordered()->get();
            Log::info('🤖 [ChatBot@sendMessage] reglas cargadas', ['count' => $rules->count()]);

            foreach ($rules as $rule) {
                // tokens() debe devolver array de strings o patrones regex
                $tokens = (array) $rule->tokens();
                Log::debug('🤖 [ChatBot@sendMessage] evaluando regla', [
                    'rule_id'     => $rule->id,
                    'use_regex'   => (bool) $rule->use_regex,
                    'match_mode'  => $rule->match_mode, // 'any' | 'all'
                    'tokens'      => $tokens,
                ]);

                if ($rule->use_regex) {
                    $matches = 0;
                    foreach ($tokens as $pattern) {
                        // Asegura delimitadores si vienen sin ellos
                        $pat = $pattern;
                        if (@preg_match($pat, '') === false) {
                            // intenta envolver con delimitadores estándar
                            $pat = '#' . str_replace('#', '\#', $pattern) . '#u';
                        }

                        $ok = @preg_match($pat, $msg) === 1;
                        if ($ok) $matches++;

                        Log::debug('🤖 [regex] probado', ['pattern' => $pat, 'ok' => $ok]);

                        if ($rule->match_mode === 'any' && $ok) {
                            Log::info('🤖 [match] regex ANY → respuesta', ['rule_id' => $rule->id]);
                            return response()->json(['ok' => true, 'reply' => $rule->response]);
                        }
                    }

                    if ($rule->match_mode === 'all' && $matches === count($tokens) && $matches > 0) {
                        Log::info('🤖 [match] regex ALL → respuesta', ['rule_id' => $rule->id]);
                        return response()->json(['ok' => true, 'reply' => $rule->response]);
                    }
                } else {
                    $hits = 0;
                    foreach ($tokens as $token) {
                        $needle = mb_strtolower((string) $token, 'UTF-8');
                        $ok = ($needle !== '') && str_contains($msgLower, $needle);
                        if ($ok) $hits++;

                        Log::debug('🤖 [plain] probado', ['token' => $needle, 'ok' => $ok]);

                        if ($rule->match_mode === 'any' && $ok) {
                            Log::info('🤖 [match] plain ANY → respuesta', ['rule_id' => $rule->id]);
                            return response()->json(['ok' => true, 'reply' => $rule->response]);
                        }
                    }

                    if ($rule->match_mode === 'all' && $hits === count($tokens) && $hits > 0) {
                        Log::info('🤖 [match] plain ALL → respuesta', ['rule_id' => $rule->id]);
                        return response()->json(['ok' => true, 'reply' => $rule->response]);
                    }
                }
            }

            // Ninguna regla aplicó
            Log::info('🤖 [ChatBot@sendMessage] sin coincidencias', ['msg' => $msg]);

            return response()->json([
                'ok'    => true,
                'reply' => 'No tengo una respuesta para eso aún. Intenta con otras palabras o cuéntame más detalles.',
            ]);

        } catch (\Throwable $e) {
            Log::error('❌ [ChatBot@sendMessage] excepción', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            // Devuelve error JSON (el front ya lo maneja)
            return response()->json([
                'ok'    => false,
                'reply' => 'Hubo un problema al responder. Intenta otra vez.',
                'error' => app()->isLocal() ? $e->getMessage() : null,
            ], 500);
        }
    }
}
