<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alertas_duplicado', function (Blueprint $table) {
            $table->increments('id_alerta');
            $table->unsignedInteger('id_usuario');
            $table->string('tipo_duplicado', 50); // dpi, numero_expediente, numero_familia
            $table->string('valor_duplicado', 50);
            $table->timestamp('fecha_deteccion')->useCurrent();
            $table->string('accion_tomada', 50)->nullable(); // ignorada, corregida

            $table->foreign('id_usuario')
                  ->references('id_usuario')
                  ->on('usuarios')
                  ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alertas_duplicado');
    }
};
