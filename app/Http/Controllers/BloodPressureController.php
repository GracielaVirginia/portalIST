<?php
namespace App\Http\Controllers;

use App\Models\BloodPressure;
use Illuminate\Http\Request;

class BloodPressureController extends Controller
{
  public function store(Request $request)
  {
    $data = $request->validate([
      'fecha'       => ['required','date'],
      'tension_sistolica'  => ['required','integer','between:50,250'],
      'tension_diastolica' => ['required','integer','between:30,150'],
      'nota'        => ['nullable','string','max:255'],
    ]);

    BloodPressure::updateOrCreate(
      ['user_id'=>auth()->id(), 'fecha'=>$data['fecha']],
      ['sistolica'=>$data['tension_sistolica'], 'diastolica'=>$data['tension_diastolica'], 'nota'=>$data['nota'] ?? null]
    );

    return back()->with('ok','Tensión guardada');
  }
}
