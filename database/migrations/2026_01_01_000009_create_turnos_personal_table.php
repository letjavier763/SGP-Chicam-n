<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('turnos_personal', function (Blueprint $table) {
            $table->increments('id_turno');
            $table->unsignedInteger('id_usuario');
            $table->date('fecha');
            $table->string('tipo_turno', 20); // matutino, vespertino, nocturno
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->text('observaciones')->nullable();

            $table->foreign('id_usuario')
                  ->references('id_usuario')
                  ->on('usuarios')
                  ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('turnos_personal');
    }
};
