<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historial_resultado', function (Blueprint $table) {
            $table->bigIncrements('cod_resultado');
            $table->unsignedBigInteger('cod_consulta_res');
            $table->unsignedBigInteger('cod_receta_res')->nullable();
            $table->unsignedSmallInteger('pos_res');            // posicion en el ranking
            $table->decimal('sco_res', 5, 2)->default(0);       // % de compatibilidad
            $table->jsonb('fal_json_res')->nullable();          // ingredientes faltantes
            $table->boolean('sel_res')->default(false);         // el usuario la abrio
            $table->timestamp('fec_cre_res', 0)->useCurrent();

            $table->foreign('cod_consulta_res')->references('cod_consulta')->on('historial_consulta')->onDelete('cascade');
            $table->foreign('cod_receta_res')->references('cod_receta')->on('recetas')->onDelete('set null');
            $table->index('cod_consulta_res');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historial_resultado');
    }
};
