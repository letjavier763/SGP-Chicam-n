<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Permitir campos nulos en pacientes para soportar el registro de datos incompletos
        DB::statement('ALTER TABLE pacientes ALTER COLUMN fecha_nacimiento DROP NOT NULL;');
        DB::statement('ALTER TABLE pacientes ALTER COLUMN apellidos DROP NOT NULL;');
        DB::statement('ALTER TABLE pacientes ALTER COLUMN sexo DROP NOT NULL;');
        DB::statement('ALTER TABLE pacientes ALTER COLUMN numero_expediente_fisico DROP NOT NULL;');
        DB::statement('ALTER TABLE pacientes ALTER COLUMN id_family DROP NOT NULL;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Nota: no se fuerza NOT NULL en reversa para prevenir fallos si existen registros con nulos
    }
};
