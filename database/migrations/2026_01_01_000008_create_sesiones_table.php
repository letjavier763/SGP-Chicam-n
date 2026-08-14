<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sesiones', function (Blueprint $table) {
            $table->increments('id_sesion');
            $table->unsignedInteger('id_usuario');
            $table->timestamp('hora_inicio')->useCurrent();
            $table->timestamp('hora_cierre')->nullable();
            $table->string('ip_equipo', 45)->nullable();
            $table->string('estado', 20)->default('activa'); // activa, cerrada, expirada

            $table->foreign('id_usuario')
                  ->references('id_usuario')
                  ->on('usuarios')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sesiones');
    }
};
