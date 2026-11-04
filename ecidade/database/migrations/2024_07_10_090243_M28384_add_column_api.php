<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class M28384AddColumnApi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(
            <<<SQL

            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;

            ALTER TABLE configuracoes.db_itensmenu
                ADD COLUMN api BOOLEAN NOT NULL DEFAULT false;

             SELECT fc_atualiza_dicionario_apartir_comentario('table column',
                   'configuracoes.db_itensmenu.api',
                   '{ "descricao": "Identifica se é api ou não.",
                      "rotulo": "Identifica se é api ou não.",
                      "rotulorel": "Identifica se é api ou não",
                      "maiusculo": false,
                      "autocompl": false,
                      "aceitatipo": 5,
                      "tamanho": 1,
                      "tipoobj": "select"
            }') ;


            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl DISABLE;
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop DISABLE;
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
        DB::connection()->getPdo()->exec(
            <<<SQL
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;

            ALTER TABLE configuracoes.db_itensmenu DROP COLUMN api;
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl DISABLE;
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop DISABLE;
SQL
        );
    }
}
