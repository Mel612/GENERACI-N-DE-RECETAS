<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('favoritos', function (Blueprint $table) {
            $table->bigIncrements('cod_favorito');
            $table->unsignedBigInteger('cod_usuario_fav');
            $table->unsignedBigInteger('cod_receta_fav');
            $table->timestamp('fec_cre_fav', 0)->useCurrent();

            $table->foreign('cod_usuario_fav')->references('cod_usuario')->on('usuarios')->onDelete('cascade');
            $table->foreign('cod_receta_fav')->references('cod_receta')->on('recetas')->onDelete('cascade');
            $table->unique(['cod_usuario_fav', 'cod_receta_fav'], 'favoritos_usuario_receta_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('favoritos');
    }
};
