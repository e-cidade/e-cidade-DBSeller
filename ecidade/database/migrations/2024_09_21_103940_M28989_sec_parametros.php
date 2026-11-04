<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M28989SecParametros extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(<<<SQL
ALTER TABLE sec_parametros ADD COLUMN ed290_correcao_de_fluxo_manual INTEGER DEFAULT 2;
SQL
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared(<<<SQL
ALTER TABLE sec_parametros DROP COLUMN ed290_habilita_correcao_de_fluxo_manual;
SQL
        );
    }
}
