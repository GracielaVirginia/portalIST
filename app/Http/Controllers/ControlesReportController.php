<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ControlesReportController extends Controller
{
    public function pdf(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'chart_base64' => ['nullable','string'],   // data:image/png;base64,...
            'start_date'   => ['nullable','date'],
            'end_date'     => ['nullable','date','after_or_equal:start_date'],
        ]);

        $start = $data['start_date'] ?? now()->subDays(30)->toDateString();
        $end   = $data['end_date']   ?? now()->toDateString();

        $glucose = $user->glucoseReadings()
            ->whereBetween('fecha', [$start, $end])
            ->orderBy('fecha','desc')->get();

        $peso = $user->weightEntries()
            ->whereBetween('fecha', [$start, $end])
            ->orderBy('fecha','desc')->get();

        $tension = $user->bloodPressures()
            ->whereBetween('fecha', [$start, $end])
            ->orderBy('fecha','desc')->get();

        $pdf = Pdf::loadView('reports.controles', [
            'user'        => $user,
            'chart_base64'=> $data['chart_base64'] ?? null,
            'glucose'     => $glucose,
            'peso'        => $peso,
            'tension'     => $tension,
            'start'       => $start,
            'end'         => $end,
        ])->setPaper('a4', 'portrait');

        return $pdf->download('controles_salud.pdf');
    }
}
