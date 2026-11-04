<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M28628Versao13Esocial extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->upDefineVersaoEsocial();
        $this->upDefineVersaoFormulario();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->downDefineVersaoFormulario();
        $this->downDefineVersaoEsocial();
    }

    private function upDefineVersaoEsocial()
    {
        $sql = <<<SQL
            INSERT INTO recursoshumanos.esocialversao
            (rh210_sequencial, rh210_versao)
            VALUES(nextval('esocialversao_rh210_sequencial_seq'::regclass), 'S1.3');
SQL;
        DB::connection()->getPdo()->exec($sql);
    }

    private function downDefineVersaoEsocial()
    {
        $sql = <<<SQL
            delete from recursoshumanos.esocialversao where trim(rh210_versao) = 'S1.3';
SQL;
        DB::connection()->getPdo()->exec($sql);
    }

    private function upDefineVersaoFormulario()
    {
        $sql = <<<SQL
            insert into
                recursoshumanos.esocialversaoformulario
            select
                nextval('esocialversaoformulario_rh211_sequencial_seq') as rh211_sequencial,
                'S1.3' as rh211_versao,
                rh211_avaliacao,
                rh211_esocialformulariotipo
            from
                recursoshumanos.esocialversaoformulario
            where
                trim(rh211_versao) = 'S1.2'
                and (rh211_avaliacao != 4000126
            and trim(rh211_versao) != 'S1.3');
SQL;
        DB::connection()->getPdo()->exec($sql);
    }

    private function downDefineVersaoFormulario()
    {
        $sql = <<<SQL
            delete from 
                recursoshumanos.esocialversaoformulario 
            where 
                trim(rh211_versao) = 'S1.3' 
                and rh211_avaliacao != 4000126;
SQL;
        DB::connection()->getPdo()->exec($sql);
    }
}
