<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuario_preferencias', function (Blueprint $table) {
            $table->unsignedBigInteger('cod_usuario_upr');
            $table->unsignedSmallInteger('cod_preferencia_upr');
            $table->timestamp('fec_cre_upr', 0)->useCurrent();

            $table->primary(['cod_usuario_upr', 'cod_preferencia_upr'], 'usuario_preferencias_pkey');
            $table->foreign('cod_usuario_upr')->references('cod_usuario')->on('usuarios')->onDelete('cascade');
            $table->foreign('cod_preferencia_upr')->references('cod_preferencia')->on('preferencias_dieta')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuario_preferencias');
    }
};
