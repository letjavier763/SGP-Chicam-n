<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bitacora', function (Blueprint $table) {
            $table->increments('id_bitacora');
            $table->unsignedInteger('id_usuario');
            $table->string('accion', 100);           // crear, editar, eliminar, consultar
            $table->string('tabla_afectada', 100);
            $table->integer('id_registro_afectado')->nullable();
            $table->text('detalle')->nullable();
            $table->string('ip_equipo', 45)->nullable();
            $table->timestamp('fecha')->useCurrent();

            $table->foreign('id_usuario')
                  ->references('id_usuario')
                  ->on('usuarios')
                  ->onDelete('restrict');

            // Índice para consultas de auditoría
            $table->index(['id_usuario', 'fecha']);
            $table->index('tabla_afectada');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bitacora');
    }
};
