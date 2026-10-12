<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historial_consulta', function (Blueprint $table) {
            $table->bigIncrements('cod_consulta');
            $table->unsignedBigInteger('cod_usuario_con');
            $table->jsonb('ing_json_con');            // ingredientes enviados
            $table->jsonb('fil_json_con')->nullable(); // filtros aplicados
            $table->text('tex_lib_con')->nullable();   // texto libre del usuario
            $table->unsignedSmallInteger('tot_res_con')->default(0);
            $table->unsignedInteger('tie_res_con')->nullable(); // ms de respuesta
            $table->timestamp('fec_cre_con', 0)->useCurrent();

            $table->foreign('cod_usuario_con')->references('cod_usuario')->on('usuarios')->onDelete('cascade');
            $table->index(['cod_usuario_con', 'fec_cre_con']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historial_consulta');
    }
};
