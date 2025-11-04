<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\TicketSoporteGalenNuevo;
use App\Models\TicketSoporteGalen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AdminGalenSupportController extends Controller
{
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'email'    => ['required','email','max:150'],
                'rut'      => ['required','string','max:20'],
                'telefono' => ['nullable','string','max:30'],
                'detalle'  => ['required','string','max:2000'],
                'archivo'  => ['nullable','file','max:5120'], // 5MB
            ]);

            $path = null;
            if ($request->hasFile('archivo')) {
                $path = $request->file('archivo')->store('support-galen', 'public');
            }

            $ticket = TicketSoporteGalen::create([
                'email'    => $data['email'],
                'rut'      => $data['rut'],
                'telefono' => $data['telefono'] ?? null,
                'detalle'  => $data['detalle'],
                'archivo'  => $path,
                'estado'   => 'pendiente',
            ]);

            // Destinatario de Galen (configurable)
            $to = config('support_galen.to') ?: env('SUPPORT_GALEN_TO', null);
            if (!$to) {
                // Fallback por si no configuras
                $to = 'garchila@galen.cl';
            }

            Mail::to($to)->send(new TicketSoporteGalenNuevo($ticket));

            Log::info('[Admin→Galen] Ticket creado y mail enviado', [
                'ticket_id' => $ticket->id,
                'to'        => $to,
                'archivo'   => $path,
            ]);

            // Si posteas desde modal normal (postback)
            return back()->with('ok', 'Ticket enviado a Galen. ID #' . $ticket->id);

        } catch (\Throwable $e) {
            Log::error('[Admin→Galen][store] '.$e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return back()->withErrors('No se pudo enviar el ticket a Galen.');
        }
    }
}
