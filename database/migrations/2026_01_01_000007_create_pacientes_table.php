<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pacientes', function (Blueprint $table) {
            $table->increments('id_paciente');
            $table->unsignedInteger('id_family');
            $table->string('nombres', 100);
            $table->string('apellidos', 100);
            $table->string('dpi', 13)->unique()->nullable();
            $table->string('numero_expediente_fisico', 50); // Mismo número del núcleo familiar
            $table->date('fecha_nacimiento');
            $table->char('sexo', 1); // M = Masculino, F = Femenino
            $table->string('telefono', 8)->nullable();
            $table->string('parentesco_familia', 50)->nullable();
            $table->timestamp('fecha_registro')->useCurrent();
            $table->boolean('activo')->default(true);

            $table->foreign('id_family')
                  ->references('id_family')
                  ->on('familias')
                  ->onDelete('restrict');

            // Índices para búsqueda rápida
            $table->index(['nombres', 'apellidos']);
            $table->index('dpi');
            $table->index('numero_expediente_fisico');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pacientes');
    }
};
