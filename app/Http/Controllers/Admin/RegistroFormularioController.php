<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\RegistroFormulario;
use App\Models\Setting;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class RegistroFormularioController extends Controller
{
    public function index()
    {
        $registros = RegistroFormulario::latest()->paginate(10);
        return Inertia::render('Admin/Registros/Index', [
            'registros' => $registros
        ]);
    }

    public function settings()
    {
        return Inertia::render('Admin/Registros/Settings', [
            'startDate' => Setting::where('key', 'form_start_date')->value('value'),
            'endDate' => Setting::where('key', 'form_end_date')->value('value'),
            'maxParticipants' => Setting::where('key', 'form_max_participants')->value('value'),
        ]);
    }

    public function updateSettings(Request $request)
    {
        $data = $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'max_participants' => 'nullable|integer|min:1',
        ]);

        Setting::updateOrCreate(['key' => 'form_start_date'], ['value' => $data['start_date']]);
        Setting::updateOrCreate(['key' => 'form_end_date'], ['value' => $data['end_date']]);
        Setting::updateOrCreate(['key' => 'form_max_participants'], ['value' => $data['max_participants']]);

        return redirect()->back()->with('success', 'Configuración actualizada.');
    }

    public function destroy(RegistroFormulario $registro)
    {
        if ($registro->documento_identidad_path) {
            Storage::disk('public')->delete($registro->documento_identidad_path);
        }
        if ($registro->sisben_path) {
            Storage::disk('public')->delete($registro->sisben_path);
        }

        // Attempt to delete the folder if empty? 
        // For now, just delete the file references.

        $registro->delete();
        return redirect()->back()->with('success', 'Registro eliminado.');
    }
}
