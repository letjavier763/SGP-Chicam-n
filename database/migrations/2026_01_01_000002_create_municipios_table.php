<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('municipios', function (Blueprint $table) {
            $table->integer('id_municipio')->primary();
            $table->integer('id_departamento');
            $table->string('nombre', 100);

            $table->foreign('id_departamento')
                  ->references('id_departamento')
                  ->on('departamentos')
                  ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('municipios');
    }
};
