<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\RegistroFormulario;
use App\Models\Setting;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PublicFormController extends Controller
{
    private function getSharedProps()
    {
        $settings = Setting::pluck('value', 'key')->toArray();
        $mainMenu = \App\Models\Menu::where('location', 'header')->where('is_active', true)->with('items')->first();

        return [
            'settings' => $settings,
            'mainMenu' => $mainMenu,
        ];
    }

    public function index()
    {
        $startDate = Setting::where('key', 'form_start_date')->value('value');
        $endDate = Setting::where('key', 'form_end_date')->value('value');
        $maxParticipants = Setting::where('key', 'form_max_participants')->value('value');

        $now = now();
        $isClosed = false;
        $message = '';

        if ($startDate && $now->lt($startDate)) {
            $isClosed = true;
            $message = 'El formulario aún no está abierto.';
        }

        if ($endDate && $now->gt($endDate)) {
            $isClosed = true;
            $message = 'El formulario ha cerrado.';
        }

        if ($maxParticipants) {
            $count = RegistroFormulario::count();
            if ($count >= $maxParticipants) {
                $isClosed = true;
                $message = 'Se ha alcanzado el número máximo de registros.';
            }
        }

        return Inertia::render('Public/FormularioRegistro', [
            'isClosed' => $isClosed,
            'message' => $message,
            ...$this->getSharedProps(),
        ]);
    }

    public function store(Request $request)
    {
        // Re-check constraints
        $startDate = Setting::where('key', 'form_start_date')->value('value');
        $endDate = Setting::where('key', 'form_end_date')->value('value');
        $maxParticipants = Setting::where('key', 'form_max_participants')->value('value');
        $now = now();

        if (
            ($startDate && $now->lt($startDate)) ||
            ($endDate && $now->gt($endDate))
        ) {
            return redirect()->back()->with('error', 'El formulario no está disponible en este momento.')->with($this->getSharedProps());
        }

        if ($maxParticipants && RegistroFormulario::count() >= $maxParticipants) {
            return redirect()->back()->with('error', 'Se ha alcanzado el límite de registros.')->with($this->getSharedProps());
        }

        $validated = $request->validate([
            'municipio' => 'required|string',
            'nombre_completo' => 'required|string',
            'fecha_nacimiento' => 'required|date',
            'tipo_documento' => 'required|string',
            'numero_documento' => 'required|string',
            'documento_identidad_path' => 'required|file|mimes:pdf|max:2048',
            'sexo' => 'required|string',
            'nacionalidad' => 'required|string',
            'zona_residencia' => 'required|string',
            'direccion' => 'required|string',
            'telefono' => 'required|string',
            'correo' => 'required|email',
            'clasificacion_sisben' => 'required|string',
            'sisben_path' => 'required|file|mimes:pdf|max:2048',
            'tiene_iniciativa' => 'required|boolean',
            'nombre_iniciativa' => 'nullable|string|required_if:tiene_iniciativa,true',
        ]);

        // Handle File Uploads
        $folderName = Str::slug($validated['nombre_completo'] . '-' . $validated['numero_documento']);

        $docPath = $request->file('documento_identidad_path')->storePubliclyAs(
            "registros/{$folderName}",
            'documento_identidad.pdf',
            'public'
        );

        $sisbenPath = $request->file('sisben_path')->storePubliclyAs(
            "registros/{$folderName}",
            'sisben.pdf',
            'public'
        );

        $validated['documento_identidad_path'] = $docPath;
        $validated['sisben_path'] = $sisbenPath;

        RegistroFormulario::create($validated);

        return redirect()->back()->with('success', 'Formulario enviado exitosamente.');
    }
}
