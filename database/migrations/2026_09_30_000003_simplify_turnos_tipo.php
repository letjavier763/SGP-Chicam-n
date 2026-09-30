<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Hacer tipo_turno nullable con valor por defecto 'dia'
        Schema::table('turnos_personal', function (Blueprint $table) {
            $table->string('tipo_turno', 20)->nullable()->default('dia')->change();
            $table->time('hora_inicio')->nullable()->change();
            $table->time('hora_fin')->nullable()->change();
        });

        // Actualizar registros existentes
        DB::table('turnos_personal')->whereNull('tipo_turno')->update(['tipo_turno' => 'dia']);
    }

    public function down(): void
    {
        Schema::table('turnos_personal', function (Blueprint $table) {
            $table->string('tipo_turno', 20)->nullable(false)->change();
            $table->time('hora_inicio')->nullable(false)->change();
            $table->time('hora_fin')->nullable(false)->change();
        });
    }
};
