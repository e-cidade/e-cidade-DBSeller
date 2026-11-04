<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M28889 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        $column = 'cadastro.ruasbairro.j16_cep';
        $json = '{
            "descricao":"Campo referente ao CEP do Bairro.",
            "rotulo":"CEP Bairro",
            "rotulorel":"CEP Bairro",
            "maiusculo":true,
            "autocompl":false,
            "aceitatipo":0,
            "tipoobj":"text"
        }';

        DB::unprepared("
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;

            ALTER TABLE ruasbairro ADD COLUMN j16_cep char(8);
            comment on column $column is '$json';

            select fc_gera_dicionario_apartir_tabela('cadastro','ruasbairro');

            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl DISABLE;
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop DISABLE;
        ");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        DB::unprepared("
            ALTER TABLE ruasbairro DROP COLUMN j16_cep;
        ");

    }
}
