<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('familias', function (Blueprint $table) {
            $table->increments('id_family');
            $table->integer('id_comunidad');
            $table->string('numero_familia', 20)->unique();
            $table->string('apellido_cabeza', 100);
            $table->string('dpi', 13)->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->timestamp('fecha_registro')->useCurrent();
            $table->boolean('activo')->default(true);

            $table->foreign('id_comunidad')
                  ->references('id_comunidad')
                  ->on('comunidades')
                  ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('familias');
    }
};
