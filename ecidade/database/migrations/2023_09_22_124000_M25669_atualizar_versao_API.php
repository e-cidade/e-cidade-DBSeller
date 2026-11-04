<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M25669AtualizarVersaoAPI extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<SQL
        insert into recursoshumanos.esocialversaoformulario (select nextval('esocialversaoformulario_rh211_sequencial_seq'), 'S1.2', rh211_avaliacao, rh211_esocialformulariotipo from recursoshumanos.esocialversaoformulario where rh211_versao = 'S1.1');
        insert into recursoshumanos.esocialversao values ((select  nextval(' esocialversao_rh210_sequencial_seq')), 'S1.2');
SQL;
        DB::connection()->getPdo()->exec($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $sql = <<<SQL
        delete from recursoshumanos.esocialversaoformulario where rh211_versao = 'S1.2';
        delete from recursoshumanos.esocialversao where rh210_versao = 'S1.2';
SQL;
        DB::connection()->getPdo()->exec($sql);
    }
}
