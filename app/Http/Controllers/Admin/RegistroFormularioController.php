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

    public function export()
    {
        $response = new \Symfony\Component\HttpFoundation\StreamedResponse(function() {
            $handle = fopen('php://output', 'w');
            // Agregar BOM para que Excel lea los caracteres UTF-8 correctamente
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // Encabezados
            fputcsv($handle, [
                'ID', 'Municipio', 'Nombre Completo', 'Fecha de Nacimiento', 'Tipo de Documento', 
                'Número de Documento', 'Sexo', 'Nacionalidad', 'Zona de Residencia', 
                'Dirección', 'Teléfono', 'Correo', 'Clasificación Sisben', 
                'Tiene Iniciativa', 'Nombre Iniciativa', 'Fecha de Registro'
            ], ';');

            RegistroFormulario::chunk(100, function($registros) use ($handle) {
                foreach($registros as $registro) {
                    fputcsv($handle, [
                        $registro->id,
                        $registro->municipio,
                        $registro->nombre_completo,
                        $registro->fecha_nacimiento ? $registro->fecha_nacimiento->format('Y-m-d') : '',
                        $registro->tipo_documento,
                        $registro->numero_documento,
                        $registro->sexo,
                        $registro->nacionalidad,
                        $registro->zona_residencia,
                        $registro->direccion,
                        $registro->telefono,
                        $registro->correo,
                        $registro->clasificacion_sisben,
                        $registro->tiene_iniciativa ? 'Sí' : 'No',
                        $registro->nombre_iniciativa,
                        $registro->created_at ? $registro->created_at->format('Y-m-d H:i:s') : ''
                    ], ';');
                }
            });
            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="registros.csv"');

        return $response;
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
