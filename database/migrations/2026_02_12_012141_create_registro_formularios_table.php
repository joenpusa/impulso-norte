<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('registro_formularios', function (Blueprint $table) {
            $table->id();
            $table->string('municipio');
            $table->string('nombre_completo');
            $table->date('fecha_nacimiento');
            $table->string('tipo_documento');
            $table->string('numero_documento');
            $table->string('documento_identidad_path')->nullable();
            $table->string('sexo');
            $table->string('nacionalidad');
            $table->string('zona_residencia');
            $table->string('direccion');
            $table->string('telefono');
            $table->string('correo');
            $table->string('clasificacion_sisben');
            $table->string('sisben_path')->nullable();
            $table->boolean('tiene_iniciativa');
            $table->string('nombre_iniciativa')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registro_formularios');
    }
};
