<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('preferencias_dieta', function (Blueprint $table) {
            $table->smallIncrements('cod_preferencia');
            $table->string('nom_pre', 80)->unique();
            $table->string('des_pre', 255)->nullable();
            $table->boolean('act_pre')->default(true);
            $table->timestamp('fec_cre_pre', 0)->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('preferencias_dieta');
    }
};
