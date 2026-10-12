<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categorias_receta', function (Blueprint $table) {
            $table->increments('cod_categoria_receta');
            $table->string('nom_car', 100)->unique();
            $table->string('des_car', 255)->nullable();
            $table->string('ico_car', 300)->nullable();
            $table->boolean('act_car')->default(true);
            $table->timestamp('fec_cre_car', 0)->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categorias_receta');
    }
};
