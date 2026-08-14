<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->increments('id_usuario');
            $table->integer('id_rol');
            $table->string('nombre_completo', 150);
            $table->string('username', 50)->unique();
            $table->string('password_hash', 255);
            $table->timestamp('ultimo_acceso')->nullable();
            $table->boolean('activo')->default(true);

            $table->foreign('id_rol')
                  ->references('id_rol')
                  ->on('roles')
                  ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
