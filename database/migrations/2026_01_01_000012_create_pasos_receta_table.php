<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pasos_receta', function (Blueprint $table) {
            $table->bigIncrements('cod_paso');
            $table->unsignedBigInteger('cod_receta_pas');
            $table->unsignedSmallInteger('num_pas');
            $table->text('ins_pas');
            $table->unsignedSmallInteger('tie_pas')->nullable(); // minutos
            $table->string('img_pas', 500)->nullable();
            $table->boolean('cri_pas')->default(false); // paso critico

            $table->foreign('cod_receta_pas')->references('cod_receta')->on('recetas')->onDelete('cascade');
            $table->unique(['cod_receta_pas', 'num_pas'], 'pasos_receta_orden_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pasos_receta');
    }
};
