<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recetas', function (Blueprint $table) {
            $table->bigIncrements('cod_receta');
            $table->unsignedInteger('cod_categoria_receta_rec');
            // Autor de la receta. Null = receta del sistema o generada por IA.
            $table->unsignedBigInteger('cod_usuario_rec')->nullable();
            $table->string('nom_rec', 200);
            $table->text('des_rec')->nullable();
            $table->unsignedSmallInteger('tie_pre_rec')->default(10); // minutos
            $table->unsignedSmallInteger('tie_coc_rec')->default(0);
            $table->unsignedSmallInteger('tie_tot_rec')->default(10);
            $table->unsignedSmallInteger('por_rec')->default(2);
            $table->string('niv_rec', 10)->default('medio');   // facil | medio | dificil
            $table->string('ori_rec', 15)->default('sistema'); // sistema | ia_generada | usuario
            $table->string('img_rec', 500)->nullable();
            $table->decimal('cal_por_rec', 7, 2)->nullable();
            // Estado de validacion nutricional (historias 9, 12 y 13)
            $table->string('est_val_rec', 15)->default('pendiente'); // pendiente | aprobada | rechazada | observada
            $table->boolean('ver_rec')->default(false);              // marcada como verificada
            $table->boolean('act_rec')->default(true);
            $table->timestamp('fec_cre_rec', 0)->useCurrent();
            $table->timestamp('fec_act_rec', 0)->nullable();
            $table->timestamp('fec_eli_rec', 0)->nullable();

            $table->foreign('cod_categoria_receta_rec')
                  ->references('cod_categoria_receta')->on('categorias_receta')->onDelete('restrict');
            $table->foreign('cod_usuario_rec')
                  ->references('cod_usuario')->on('usuarios')->onDelete('set null');

            $table->index('est_val_rec');
            $table->index('ori_rec');
            $table->index(['act_rec', 'ver_rec']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recetas');
    }
};
