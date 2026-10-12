<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('validaciones_receta', function (Blueprint $table) {
            $table->bigIncrements('cod_validacion');
            $table->unsignedBigInteger('cod_receta_vre');
            $table->unsignedBigInteger('cod_usuario_vre'); // nutricionista que reviso
            $table->string('est_vre', 15); // aprobada | rechazada | observada
            $table->text('obs_vre')->nullable();
            $table->boolean('val_ing_vre')->default(false); // ingredientes validados
            $table->boolean('val_pas_vre')->default(false); // pasos validados
            $table->boolean('val_nut_vre')->default(false); // informacion nutricional validada
            $table->timestamp('fec_rev_vre', 0)->useCurrent();

            $table->foreign('cod_receta_vre')->references('cod_receta')->on('recetas')->onDelete('cascade');
            $table->foreign('cod_usuario_vre')->references('cod_usuario')->on('usuarios')->onDelete('restrict');
            $table->index(['cod_receta_vre', 'fec_rev_vre']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('validaciones_receta');
    }
};
