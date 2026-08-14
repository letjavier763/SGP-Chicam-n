<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comunidades', function (Blueprint $table) {
            $table->integer('id_comunidad')->primary();
            $table->integer('id_municipio');
            $table->string('nombre', 100);
            $table->string('zona', 50)->nullable();

            $table->foreign('id_municipio')
                  ->references('id_municipio')
                  ->on('municipios')
                  ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comunidades');
    }
};
