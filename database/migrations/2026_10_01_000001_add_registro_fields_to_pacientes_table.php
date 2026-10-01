<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pacientes', function (Blueprint $table) {
            $table->integer('numero_registro')->nullable()->after('numero_expediente_fisico');
            $table->string('descripcion_registro', 150)->nullable()->after('numero_registro');
        });
    }

    public function down(): void
    {
        Schema::table('pacientes', function (Blueprint $table) {
            $table->dropColumn(['numero_registro', 'descripcion_registro']);
        });
    }
};
