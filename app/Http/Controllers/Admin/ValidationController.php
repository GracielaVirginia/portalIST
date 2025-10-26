<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Validation; // Asegúrate de tener un modelo Validation

class ValidationController extends Controller
{
    public function index()
    {
        $validations = Validation::all(); // Ejemplo: Obtener todas las validaciones
        return view('admin.validations.index', compact('validations'));
    }
}
