{{-- resources/views/reports/controles.blade.php --}}
<!doctype html>
<html>
<head>
  <meta charset="utf-8"/>
  <style>
    body{ font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111; }
    h1{ font-size: 18px; margin: 0 0 8px; }
    h2{ font-size: 14px; margin: 16px 0 6px; }
    table{ width: 100%; border-collapse: collapse; margin-top: 6px; }
    th, td{ border: 1px solid #ddd; padding: 6px 8px; font-size: 11px; }
    th{ background: #f2f2f2; text-align: left; }
    .muted{ color:#666; font-size: 11px; }
  </style>
</head>
<body>
  <h1>Reporte de Controles de Salud</h1>
  <div class="muted">Paciente: {{ $user->name }} — Fecha: {{ now()->format('d/m/Y') }}</div>

  <h2>Evolución</h2>
  @if(str_starts_with($chart_base64, 'data:image'))
    <img src="{{ $chart_base64 }}" style="width:100%; max-height:420px; object-fit:contain;">
  @else
    <p class="muted">No se adjuntó imagen del gráfico.</p>
  @endif

  <h2>Últimas mediciones</h2>
  <table>
    <thead>
      <tr>
        <th>Fecha</th>
        <th>Glucosa (mg/dL)</th>
        <th>Peso (kg)</th>
        <th>Tensión (mmHg)</th>
      </tr>
    </thead>
    <tbody>
      @for($i=0;$i<10;$i++)
        @php
          $g = $glucose[$i] ?? null;
          $p = $peso[$i] ?? null;
          $t = $tension[$i] ?? null;
        @endphp
        <tr>
          <td>{{ $g?->fecha?->format('d/m/Y') ?? $p?->fecha?->format('d/m/Y') ?? $t?->fecha?->format('d/m/Y') ?? '' }}</td>
          <td>{{ $g?->valor ?? '' }}</td>
          <td>{{ $p?->valor ?? '' }}</td>
          <td>
            @if($t) {{ $t->sistolica }}/{{ $t->diastolica }} @endif
          </td>
        </tr>
      @endfor
    </tbody>
  </table>
</body>
</html>
