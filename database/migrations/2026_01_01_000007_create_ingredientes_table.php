<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ingredientes', function (Blueprint $table) {
            $table->bigIncrements('cod_ingrediente');
            $table->unsignedInteger('cod_categoria_ingrediente_ing');
            $table->string('nom_ing', 150);
            $table->string('sin_ing', 300)->nullable();   // sinonimos separados por coma
            $table->decimal('cal_ing', 7, 2)->nullable(); // kcal por 100 g
            $table->decimal('pro_ing', 6, 2)->nullable();
            $table->decimal('car_ing', 6, 2)->nullable();
            $table->decimal('gra_ing', 6, 2)->nullable();
            $table->boolean('ale_ing')->default(false);   // alergeno
            $table->string('img_ing', 300)->nullable();
            $table->boolean('act_ing')->default(true);
            $table->timestamp('fec_cre_ing', 0)->useCurrent();
            $table->timestamp('fec_act_ing', 0)->nullable();

            $table->foreign('cod_categoria_ingrediente_ing')
                  ->references('cod_categoria_ingrediente')->on('categorias_ingrediente')
                  ->onDelete('restrict');
            $table->unique(['nom_ing', 'cod_categoria_ingrediente_ing'], 'ingredientes_nombre_categoria_unique');
            $table->index('act_ing');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ingredientes');
    }
};
