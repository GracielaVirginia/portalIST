<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TicketSoporte;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class SoporteController extends Controller
{
    public function index()
{
    $tickets = \App\Models\TicketSoporte::latest()->get();
    return view('admin.tickets.index', compact('tickets'));
}
    public function create()
    {
        return view('soporte.enviar');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'email'     => 'required|email',
            'rut'       => 'required|string|max:20',
            'telefono'  => 'nullable|string|max:30',
            'detalle'   => 'required|string|min:10',
            'archivo'   => 'nullable|file|max:2048',
        ]);

        // Subir archivo si lo hay
        if ($request->hasFile('archivo')) {
            $data['archivo'] = $request->file('archivo')->store('tickets', 'public');
        }

        $ticket = TicketSoporte::create($data);

        // Enviar notificación al administrador (ejemplo)
        Mail::raw("Nuevo ticket recibido de {$ticket->email}\n\n{$ticket->detalle}", function($msg) use ($ticket) {
            $msg->to('admin@tuportal.cl')
                ->subject('Nuevo ticket de soporte #' . $ticket->id);
        });

        return back()->with('success', 'Tu ticket fue enviado correctamente. Te contactaremos pronto.');
    }
    public function show(\App\Models\TicketSoporte $ticket)
{
    return view('admin.tickets.show', compact('ticket'));
}
public function resolve(\App\Models\TicketSoporte $ticket)
{
    if ($ticket->estado !== 'resuelto') {
        $ticket->estado = 'resuelto';
        $ticket->save();
    }

    return response()->json([
        'ok'     => true,
        'id'     => $ticket->id,
        'estado' => $ticket->estado,
    ]);
}

}
