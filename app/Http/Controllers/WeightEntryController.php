<?php
namespace App\Http\Controllers;

use App\Models\WeightEntry;
use Illuminate\Http\Request;

class WeightEntryController extends Controller
{
  public function store(Request $request)
  {
    $data = $request->validate([
      'fecha' => ['required','date'],
      'peso'  => ['required','numeric','between:20,400'],
      'nota'  => ['nullable','string','max:255'],
    ]);

    WeightEntry::updateOrCreate(
      ['user_id'=>auth()->id(), 'fecha'=>$data['fecha']],
      ['valor'=>$data['peso'], 'nota'=>$data['nota'] ?? null]
    );

    return back()->with('ok','Peso guardado');
  }
}
