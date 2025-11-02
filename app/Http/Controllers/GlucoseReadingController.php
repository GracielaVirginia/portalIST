<?php
namespace App\Http\Controllers;

use App\Models\GlucoseReading;
use Illuminate\Http\Request;

class GlucoseReadingController extends Controller
{
  public function store(Request $request)
  {
    $data = $request->validate([
      'fecha'   => ['required','date'],
      'glucosa' => ['required','integer','between:40,600'],
      'nota'    => ['nullable','string','max:255'],
    ]);

    GlucoseReading::updateOrCreate(
      ['user_id'=>auth()->id(), 'fecha'=>$data['fecha']],
      ['valor'=>$data['glucosa'], 'nota'=>$data['nota'] ?? null]
    );

    return back()->with('ok','Glucosa guardada');
  }
}
