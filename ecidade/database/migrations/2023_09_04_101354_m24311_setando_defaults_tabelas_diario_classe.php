<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M24311SetandoDefaultsTabelasDiarioClasse extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE escola.diario_classe_bncc_habilidade ALTER COLUMN ed156_codigo SET DEFAULT nextval('escola.diario_classe_bncc_habilidade_ed156_codigo_seq')");
        DB::statement("ALTER TABLE escola.diario_classe_bncc_habilidade_referencial ALTER COLUMN ed169_codigo SET DEFAULT nextval('escola.diario_classe_bncc_habilidade_referencial_ed169_codigo_seq')");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE escola.diario_classe_bncc_habilidade ALTER COLUMN ed156_codigo SET DEFAULT 0");
        DB::statement("ALTER TABLE escola.diario_classe_bncc_habilidade_referencial ALTER COLUMN ed169_codigo SET DEFAULT 0");
    }
}
