<!doctype html>
<html lang="es">
<body style="font-family: Arial, sans-serif;">
  <h2>Nuevo ticket de soporte (Admin → Galen)</h2>
  <p><strong>Email:</strong> {{ $ticket->email }}</p>
  <p><strong>RUT:</strong> {{ $ticket->rut }}</p>
  <p><strong>Teléfono:</strong> {{ $ticket->telefono ?: '—' }}</p>
  <p><strong>Detalle:</strong></p>
  <pre style="white-space: pre-wrap;">{{ $ticket->detalle }}</pre>

  @if($ticket->archivo)
    <p><strong>Archivo:</strong> {{ $ticket->archivo }}</p>
  @endif

  <p style="color:#888;">ID Ticket: #{{ $ticket->id }} · Estado: {{ $ticket->estado }}</p>
</body>
</html>
