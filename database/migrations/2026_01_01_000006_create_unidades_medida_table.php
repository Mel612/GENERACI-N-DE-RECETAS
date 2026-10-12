<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('unidades_medida', function (Blueprint $table) {
            $table->smallIncrements('cod_unidad');
            $table->string('nom_uni', 50);
            $table->string('abr_uni', 10)->unique();
            $table->string('tip_uni', 20);
            // Factor de conversion hacia la unidad base de su tipo:
            // masa -> gramo, volumen -> mililitro, unidad -> pieza
            $table->decimal('fac_uni', 12, 6)->default(1);
            $table->boolean('act_uni')->default(true);

            $table->index('tip_uni');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unidades_medida');
    }
};
