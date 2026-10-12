<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ingredientes_receta', function (Blueprint $table) {
            $table->bigIncrements('cod_ingrediente_receta');
            $table->unsignedBigInteger('cod_receta_inr');
            $table->unsignedBigInteger('cod_ingrediente_inr');
            $table->unsignedSmallInteger('cod_unidad_inr');
            $table->decimal('can_inr', 10, 3);
            $table->boolean('opc_inr')->default(false); // ingrediente opcional
            $table->unsignedSmallInteger('ord_inr')->default(99);
            $table->string('not_inr', 200)->nullable();

            $table->foreign('cod_receta_inr')->references('cod_receta')->on('recetas')->onDelete('cascade');
            $table->foreign('cod_ingrediente_inr')->references('cod_ingrediente')->on('ingredientes')->onDelete('restrict');
            $table->foreign('cod_unidad_inr')->references('cod_unidad')->on('unidades_medida')->onDelete('restrict');

            $table->unique(['cod_receta_inr', 'cod_ingrediente_inr'], 'ingredientes_receta_unique');
            $table->index('cod_ingrediente_inr');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ingredientes_receta');
    }
};
