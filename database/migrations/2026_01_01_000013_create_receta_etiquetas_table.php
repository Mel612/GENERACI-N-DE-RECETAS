<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('receta_etiquetas', function (Blueprint $table) {
            $table->unsignedBigInteger('cod_receta_ret');
            $table->unsignedInteger('cod_etiqueta_ret');
            $table->timestamp('fec_cre_ret', 0)->useCurrent();

            $table->primary(['cod_receta_ret', 'cod_etiqueta_ret'], 'receta_etiquetas_pkey');
            $table->foreign('cod_receta_ret')->references('cod_receta')->on('recetas')->onDelete('cascade');
            $table->foreign('cod_etiqueta_ret')->references('cod_etiqueta')->on('etiquetas')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('receta_etiquetas');
    }
};
