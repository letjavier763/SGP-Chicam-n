<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registros_llegada', function (Blueprint $table) {
            $table->increments('id_registro');
            $table->unsignedInteger('id_paciente');
            $table->unsignedInteger('id_turno');
            $table->date('fecha');
            $table->time('hora_llegada');
            $table->boolean('es_nuevo')->default(false); // true = primer visita
            $table->text('observaciones')->nullable();

            $table->foreign('id_paciente')
                  ->references('id_paciente')
                  ->on('pacientes')
                  ->onDelete('restrict');

            $table->foreign('id_turno')
                  ->references('id_turno')
                  ->on('turnos_personal')
                  ->onDelete('restrict');

            $table->index('fecha');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registros_llegada');
    }
};
