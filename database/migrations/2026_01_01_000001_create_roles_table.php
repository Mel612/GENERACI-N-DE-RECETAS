<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->smallIncrements('cod_rol');
            $table->string('nom_rol', 50)->unique();
            $table->string('des_rol', 255)->nullable();
            $table->boolean('act_rol')->default(true);
            $table->timestamp('fec_cre_rol', 0)->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
