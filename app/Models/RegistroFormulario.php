<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegistroFormulario extends Model
{
    protected $fillable = [
        'municipio',
        'nombre_completo',
        'fecha_nacimiento',
        'tipo_documento',
        'numero_documento',
        'documento_identidad_path',
        'sexo',
        'nacionalidad',
        'zona_residencia',
        'direccion',
        'telefono',
        'correo',
        'clasificacion_sisben',
        'sisben_path',
        'tiene_iniciativa',
        'nombre_iniciativa',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'tiene_iniciativa' => 'boolean',
    ];
}
