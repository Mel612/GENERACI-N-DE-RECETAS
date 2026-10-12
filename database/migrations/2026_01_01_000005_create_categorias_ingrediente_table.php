<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categorias_ingrediente', function (Blueprint $table) {
            $table->increments('cod_categoria_ingrediente');
            $table->string('nom_cat', 100)->unique();
            $table->string('des_cat', 255)->nullable();
            $table->string('ico_cat', 300)->nullable();
            $table->boolean('act_cat')->default(true);
            $table->timestamp('fec_cre_cat', 0)->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categorias_ingrediente');
    }
};
