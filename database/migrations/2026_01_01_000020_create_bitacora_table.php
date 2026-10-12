<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bitacora', function (Blueprint $table) {
            $table->bigIncrements('cod_bitacora');
            $table->unsignedBigInteger('cod_usuario_bit')->nullable();
            $table->string('acc_bit', 60);              // login, crear, editar, eliminar, generar...
            $table->string('tab_bit', 60)->nullable();  // tabla afectada
            $table->unsignedBigInteger('reg_bit')->nullable(); // id del registro afectado
            $table->jsonb('det_bit')->nullable();       // detalle del cambio
            $table->string('ip_bit', 45)->nullable();
            $table->timestamp('fec_cre_bit', 0)->useCurrent();

            $table->foreign('cod_usuario_bit')->references('cod_usuario')->on('usuarios')->onDelete('set null');
            $table->index(['acc_bit', 'fec_cre_bit']);
            $table->index('cod_usuario_bit');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bitacora');
    }
};
