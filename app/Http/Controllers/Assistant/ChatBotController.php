<?php

namespace App\Http\Controllers\Assistant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AssistantRule;

class ChatBotController extends Controller
{
public function sendMessage(Request $request)
{
    $msg = trim((string) $request->input('message',''));

    if ($msg === '') {
        return response()->json(['reply' => 'Escribe tu consulta, por favor.']);
    }

    $msgLower = mb_strtolower($msg, 'UTF-8');

    // Carga reglas activas ordenadas por prioridad
    $rules = \App\Models\AssistantRule::active()->ordered()->get();

    foreach ($rules as $rule) {
        $tokens = $rule->tokens();

        if ($rule->use_regex) {
            $matches = 0;
            foreach ($tokens as $pattern) {
                $ok = @preg_match($pattern, $msg) === 1;
                if ($ok) $matches++;
                if ($rule->match_mode === 'any' && $ok) {
                    return response()->json(['reply' => $rule->response]);
                }
            }
            if ($rule->match_mode === 'all' && $matches === count($tokens) && $matches > 0) {
                return response()->json(['reply' => $rule->response]);
            }
        } else {
            $hits = 0;
            foreach ($tokens as $token) {
                $needle = mb_strtolower($token, 'UTF-8');
                if ($needle !== '' && str_contains($msgLower, $needle)) {
                    $hits++;
                    if ($rule->match_mode === 'any') {
                        return response()->json(['reply' => $rule->response]);
                    }
                }
            }
            if ($rule->match_mode === 'all' && $hits === count($tokens) && $hits > 0) {
                return response()->json(['reply' => $rule->response]);
            }
        }
    }

    // 👉 Mensaje por defecto si no coincide ninguna regla
    return response()->json([
        'reply' => 'No tengo una respuesta para eso aún. Intenta con otras palabras o cuéntame más detalles.'
    ]);
}
}
