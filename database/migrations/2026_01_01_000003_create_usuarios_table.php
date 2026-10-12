<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->bigIncrements('cod_usuario');
            $table->unsignedSmallInteger('cod_rol_usu')->default(3);
            $table->string('nom_usu', 100);
            $table->string('cor_usu', 255)->unique();
            $table->string('con_usu', 255);
            $table->string('fot_usu', 500)->nullable();
            $table->boolean('act_usu')->default(true);
            $table->timestamp('ult_acc_usu', 0)->nullable();
            $table->rememberToken();
            $table->timestamp('fec_cre_usu', 0)->useCurrent();
            $table->timestamp('fec_act_usu', 0)->nullable();
            $table->timestamp('fec_eli_usu', 0)->nullable();

            $table->foreign('cod_rol_usu')->references('cod_rol')->on('roles')
                  ->onUpdate('cascade')->onDelete('restrict');
            $table->index('act_usu');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
