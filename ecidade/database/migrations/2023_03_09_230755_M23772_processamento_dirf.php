<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M23772ProcessamentoDirf extends Migration
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

            ALTER TABLE pessoal.rhdirfgeracao add column if not exists rh95_instit integer;

            ALTER TABLE pessoal.rhdirfgeracao ADD CONSTRAINT rhdirfgeracao_instit_fk
                FOREIGN KEY (rh95_instit) REFERENCES configuracoes.db_config(codigo);

            SELECT fc_atualiza_dicionario_apartir_comentario('table column',
                   'pessoal.rhdirfgeracao.rh95_instit',
                   '{ "descricao": "Cód. da Instituição",
                      "rotulo": "Cód. da Instituição",
                      "rotulorel": "Cód. da Instituição",
                      "maiusculo": false,
                      "autocompl": false,
                      "aceitatipo": 1,
                      "tamanho": 10,
                      "tipoobj": "text"
                    }') ;

            UPDATE pessoal.rhdirfgeracao
               SET rh95_instit = (select codigo from db_config where cgc = trim(rh95_fontepagadora) order by codigo limit 1);

            ALTER TABLE pessoal.rhdirfgeracao alter column rh95_instit set default 0;

            ALTER TABLE pessoal.rhdirfgeracao alter column rh95_instit set not null;

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

            ALTER TABLE rhdirfgeracao drop column if exists rh95_instit;

            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl DISABLE;
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop DISABLE;
SQL
        );
    }
}
