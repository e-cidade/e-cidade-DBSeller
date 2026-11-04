<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M23511AdicionaTabelaRetencaonaturezarendimento extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<SQL

        -- trigger dicionario
        ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;
        ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;

        CREATE TABLE empenho.retencaonaturezarendimento (
	        e168_sequencial serial4 NOT NULL,
	        e168_retencaoreceitas int4 NOT NULL,
	        e168_naturezarendimento int4 NOT NULL,
	        CONSTRAINT retencaonaturezarendimento_pk PRIMARY KEY (e168_sequencial),
	        CONSTRAINT retencaonaturezarendimento_fk FOREIGN KEY (e168_retencaoreceitas) REFERENCES empenho.retencaoreceitas(e23_sequencial) ON DELETE CASCADE ON UPDATE CASCADE,
	        CONSTRAINT retencaonaturezarendimento_fk_1 FOREIGN KEY (e168_naturezarendimento) REFERENCES empenho.naturezarendimento(e167_sequencial) ON DELETE CASCADE ON UPDATE CASCADE
        );

        COMMENT ON TABLE empenho.retencaonaturezarendimento IS '{
            "descricao": "Natureza de Rendimento da Retencao",
            "sigla": "e168",
            "dataincl": "2023-09-13",
            "rotulo": "Tabela",
            "tipotabela": 0,
            "naolibclass": false,
            "naolibfunc": false,
            "naolibprog": false,
            "naolibform": false
        }';

        -- Column comments

        COMMENT ON COLUMN empenho.retencaonaturezarendimento.e168_sequencial IS '{
            "descricao": "e168_sequencial",
            "rotulo": "e168_sequencial",
            "rotulorel": "e168_sequencial",
            "maiusculo": false,
            "autocompl": false,
            "aceitatipo": 1,
            "tamanho": 10,
            "tipoobj": "text"
        }';

        COMMENT ON COLUMN empenho.retencaonaturezarendimento.e168_retencaoreceitas IS '{
            "descricao": "e168_retencaoreceitas",
            "rotulo": "e168_retencaoreceitas",
            "rotulorel": "e168_retencaoreceitas",
            "maiusculo": false,
            "autocompl": false,
            "aceitatipo": 1,
            "tamanho": 10,
            "tipoobj": "text"
        }';

        COMMENT ON COLUMN empenho.retencaonaturezarendimento.e168_naturezarendimento IS '{
            "descricao": "e168_naturezarendimento",
            "rotulo": "e168_naturezarendimento",
            "rotulorel": "e168_naturezarendimento",
            "maiusculo": false,
            "autocompl": false,
            "aceitatipo": 1,
            "tamanho": 10,
            "tipoobj": "text"
        }';

        -- auditoria / dicionario
        SELECT configuracoes.fc_auditoria_cria_funcao('empenho.retencaonaturezarendimento');
        SELECT fc_gera_dicionario_apartir_tabela('empenho', 'retencaonaturezarendimento');

        -- desabilita trigger
        ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl DISABLE;
        ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop DISABLE;
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
        DB::statement("DROP TABLE retencaonaturezarendimento");
    }
}
