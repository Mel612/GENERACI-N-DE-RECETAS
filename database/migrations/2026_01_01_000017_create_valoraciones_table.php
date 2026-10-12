<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('valoraciones', function (Blueprint $table) {
            $table->bigIncrements('cod_valoracion');
            $table->unsignedBigInteger('cod_usuario_val');
            $table->unsignedBigInteger('cod_receta_val');
            $table->unsignedSmallInteger('pun_val'); // 1 a 5
            $table->text('com_val')->nullable();
            $table->timestamp('fec_cre_val', 0)->useCurrent();
            $table->timestamp('fec_act_val', 0)->nullable();

            $table->foreign('cod_usuario_val')->references('cod_usuario')->on('usuarios')->onDelete('cascade');
            $table->foreign('cod_receta_val')->references('cod_receta')->on('recetas')->onDelete('cascade');
            $table->unique(['cod_usuario_val', 'cod_receta_val'], 'valoraciones_usuario_receta_unique');
        });

        // Una receta se puntua de 1 a 5, nada fuera de ese rango.
        DB::statement('ALTER TABLE valoraciones ADD CONSTRAINT valoraciones_puntaje_check CHECK (pun_val BETWEEN 1 AND 5)');
    }

    public function down(): void
    {
        Schema::dropIfExists('valoraciones');
    }
};
