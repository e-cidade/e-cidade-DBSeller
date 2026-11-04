<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M28310CriarColunaRiscoAutomatico extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //VERIFICAR DROP DE COMENTARIO
        DB::unprepared(<<<SQL
        ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;
        ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;
        select configuracoes.fc_auditoria_remove_funcao('issqn.parissqn');

        ALTER TABLE issqn.parissqn ADD q60_riscoautomatico BOOLEAN NOT NULL DEFAULT FALSE;

        select configuracoes.fc_auditoria_cria_funcao('issqn.parissqn');


        COMMENT ON COLUMN issqn.parissqn.q60_riscoautomatico IS
        '{
           "descricao": "Gerar risco de inscrição automático",
           "rotulo": "Risco de inscr. automático",
           "rotulorel": "Risco de inscr. automático",
           "maiusculo": false,
           "autocompl": false,
           "aceitatipo": 5,
           "tamanho": 10,
           "tipoobj": "select"
        }';

        SELECT fc_gera_dicionario_apartir_tabela('issqn', 'parissqn');
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
        DB::unprepared(<<<SQL
        select configuracoes.fc_auditoria_remove_funcao('issqn.parissqn');
        ALTER TABLE issqn.parissqn DROP q60_riscoautomatico;
        select configuracoes.fc_auditoria_cria_funcao('issqn.parissqn');
SQL
    );
    }
}
