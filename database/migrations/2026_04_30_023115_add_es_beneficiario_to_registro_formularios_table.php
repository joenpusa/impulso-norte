<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('registro_formularios', function (Blueprint $table) {
            $table->boolean('es_beneficiario')->default(false)->after('nombre_iniciativa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('registro_formularios', function (Blueprint $table) {
            $table->dropColumn('es_beneficiario');
        });
    }
};
