<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reportes_diarios', function (Blueprint $table) {
            $table->increments('id_reporte');
            $table->unsignedInteger('id_turno');
            $table->date('fecha');
            $table->integer('total_pacientes')->default(0);
            $table->integer('total_nuevos')->default(0);
            $table->integer('total_recurrentes')->default(0);
            $table->integer('tiempo_promedio_registro_seg')->nullable(); // en segundos
            $table->timestamp('generado_en')->useCurrent();

            $table->foreign('id_turno')
                  ->references('id_turno')
                  ->on('turnos_personal')
                  ->onDelete('restrict');

            $table->index('fecha');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reportes_diarios');
    }
};
