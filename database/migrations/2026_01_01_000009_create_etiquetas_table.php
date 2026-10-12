<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('etiquetas', function (Blueprint $table) {
            $table->increments('cod_etiqueta');
            $table->string('nom_eti', 80)->unique();
            $table->string('col_eti', 7)->default('#2E74B5');
            $table->boolean('act_eti')->default(true);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('etiquetas');
    }
};
