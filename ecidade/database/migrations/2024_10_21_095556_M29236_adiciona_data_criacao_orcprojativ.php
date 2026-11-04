<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M29236AdicionaDataCriacaoOrcprojativ extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;

            ALTER TABLE orcamento.orcprojativ ADD COLUMN o55_datacriacao date;


            SELECT fc_atualiza_dicionario_apartir_comentario('table column',
                   'ocamento.orcprojativ.o55_datacriacao',
                   '{ "descricao": "Data Criação",
                      "rotulo": "Data Criação",
                      "rotulorel": "Data Criação",
                      "maiusculo": false,
                      "autocompl": false,
                      "aceitatipo": 0,
                      "tamanho": 1,
                      "tipoobj": "text"
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
        DB::connection()->getPdo()->exec(<<<SQL
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;

            ALTER TABLE orcamento.orcprojativ DROP COLUMN o55_datacriacao;


            SELECT fc_atualiza_dicionario_apartir_comentario('table column',
                   'ocamento.orcprojativ.o55_datacriacao',
                   '{ "descricao": "Data Criação",
                      "rotulo": "Data Criação",
                      "rotulorel": "Data Criação",
                      "maiusculo": false,
                      "autocompl": false,
                      "aceitatipo": 0,
                      "tamanho": 1,
                      "tipoobj": "text"
                    }') ;

            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl DISABLE;
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop DISABLE;                   
SQL
        );
    }
}
