<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M27169AdicionaCampoRubrica extends Migration
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
            ALTER TABLE pessoal.servidoroperadorasaudedependente ADD rh223_rubrica varchar(4);
            COMMENT ON COLUMN pessoal.servidoroperadorasaudedependente.rh223_rubrica IS 
            '{
                "descricao":"Rubrica Dependente",
                "rotulo":"Rubrica Dependente",
                "rotulorel":"Rubrica Dependente",
                "maiusculo":false,
                "autocompl":false,
                "aceitatipo":1,
                "tamanho": 4,
                "tipoobj":"text"
            }';
            SELECT fc_gera_dicionario_apartir_tabela('pessoal', 'servidoroperadorasaudedependente');
            SELECT configuracoes.fc_auditoria_cria_funcao('pessoal.servidoroperadorasaudedependente');

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
            SELECT fc_remove_dicionario_tabela('pessoal', 'servidoroperadorasaudedependente');
            ALTER TABLE pessoal.servidoroperadorasaudedependente DROP COLUMN rh223_rubrica;
            select configuracoes.fc_auditoria_remove_funcao('pessoal.servidoroperadorasaudedependente');
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl DISABLE;
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop DISABLE;
SQL
        );
    }
}
