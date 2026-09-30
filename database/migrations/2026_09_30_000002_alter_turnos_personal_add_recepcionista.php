<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('turnos_personal', function (Blueprint $table) {
            // Hacer nullable el id_usuario para que no sea obligatorio
            $table->unsignedInteger('id_recepcionista')->nullable()->after('id_turno');
            $table->string('nombre_recepcionista', 150)->nullable()->after('id_recepcionista');

            $table->foreign('id_recepcionista')
                  ->references('id_recepcionista')
                  ->on('recepcionistas')
                  ->onDelete('set null');
        });

        // Hacer el id_usuario nullable
        Schema::table('turnos_personal', function (Blueprint $table) {
            $table->unsignedInteger('id_usuario')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('turnos_personal', function (Blueprint $table) {
            $table->dropForeign(['id_recepcionista']);
            $table->dropColumn(['id_recepcionista', 'nombre_recepcionista']);
            $table->unsignedInteger('id_usuario')->nullable(false)->change();
        });
    }
};
