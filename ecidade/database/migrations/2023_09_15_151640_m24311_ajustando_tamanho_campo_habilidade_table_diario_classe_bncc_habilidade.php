<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M24311AjustandoTamanhoCampoHabilidadeTableDiarioClasseBnccHabilidade extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE diario_classe_bncc_habilidade ALTER COLUMN ed156_habilidade type VARCHAR(20)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE diario_classe_bncc_habilidade ALTER COLUMN ed156_habilidade type VARCHAR(10)');
    }
}
