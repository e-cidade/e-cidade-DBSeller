<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M27392SimulacaocalculoCfiptu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(<<<SQL
        ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;
        ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;
        select configuracoes.fc_auditoria_remove_funcao('cadastro.cfiptu');

        ALTER TABLE cadastro.cfiptu ADD COLUMN j18_simulacaocalculo BOOLEAN DEFAULT FALSE;

        select configuracoes.fc_auditoria_cria_funcao('cadastro.cfiptu');

        COMMENT ON COLUMN cadastro.cfiptu.j18_simulacaocalculo IS
        '{
           "descricao": "Permite simulaчуo do calculo de IPTU",
           "rotulo": "Habilita Simulaчуo do calculo de IPTU",
           "rotulorel": "Habilita Simulaчуo do calculo de IPTU",
           "maiusculo": false,
           "autocompl": false,
           "aceitatipo": 5,
           "tamanho": 10,
           "tipoobj": "select"
        }';

        SELECT fc_gera_dicionario_apartir_tabela('cadastro', 'cfiptu');
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
        select configuracoes.fc_auditoria_remove_funcao('cadastro.cfiptu');
        ALTER TABLE cadastro.cfiptu DROP j18_simulacaocalculo;
        select configuracoes.fc_auditoria_cria_funcao('cadastro.cfiptu');
SQL
    );
    }
}
