<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OtherSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class OtherSettingController extends Controller
{
    /**
     * Muestra el formulario de configuración.
     */
    public function edit()
    {
        $settings = OtherSetting::first();

        // Si aún no existe registro, ponemos valores por defecto
        if (!$settings) {
            $settings = new OtherSetting([
                'session_timeout' => 20,
                'font_family' => 'Inter',
            ]);
        }

        $availableFonts = ['Inter', 'Roboto', 'Poppins', 'Open Sans', 'Lato', 'Nunito'];

        return view('admin.settings.other', [
            'settings' => $settings,
            'availableFonts' => $availableFonts,
            'allowedTimeouts' => [5, 10, 15, 20, 30],
        ]);
    }

    /**
     * Guarda los valores de configuración.
     */
    public function update(Request $request)
    {
        $data = $request->validate([
            'session_timeout' => 'required|integer|in:5,10,15,20,30',
            'font_family'     => 'required|string|max:50',
        ]);

        OtherSetting::updateOrCreate(['id' => 1], $data);

        // Limpiar cache para que AppServiceProvider lea el nuevo valor
        Cache::forget('other_settings');

        return back()->with('success', 'Configuraciones guardadas correctamente.');
    }
}
