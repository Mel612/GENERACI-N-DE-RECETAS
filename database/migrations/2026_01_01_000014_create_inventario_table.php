<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventario', function (Blueprint $table) {
            $table->bigIncrements('cod_inventario');
            $table->unsignedBigInteger('cod_usuario_inv');
            $table->unsignedBigInteger('cod_ingrediente_inv');
            $table->unsignedSmallInteger('cod_unidad_inv');
            $table->decimal('can_inv', 10, 3)->default(1);
            $table->date('fec_cad_inv')->nullable();
            $table->string('not_inv', 300)->nullable();
            $table->boolean('act_inv')->default(true);
            $table->timestamp('fec_cre_inv', 0)->useCurrent();
            $table->timestamp('fec_act_inv', 0)->nullable();

            $table->foreign('cod_usuario_inv')->references('cod_usuario')->on('usuarios')->onDelete('cascade');
            $table->foreign('cod_ingrediente_inv')->references('cod_ingrediente')->on('ingredientes')->onDelete('cascade');
            $table->foreign('cod_unidad_inv')->references('cod_unidad')->on('unidades_medida')->onDelete('restrict');

            $table->unique(['cod_usuario_inv', 'cod_ingrediente_inv'], 'inventario_usuario_ingrediente_unique');
            $table->index('fec_cad_inv');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventario');
    }
};
