<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M28310CriarHistRiscoInscricoes extends Migration
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
            CREATE TABLE issqn.histriscoinscr (
                q201_sequencial SERIAL NOT NULL,
                q201_inscr INTEGER,
                q201_risco VARCHAR(2),
                q201_dtrisco timestamp,
                PRIMARY KEY (q201_sequencial),
                FOREIGN KEY (q201_inscr) REFERENCES issqn.issbase(q02_inscr)
            );
SQL
        );
        DB::unprepared(<<<SQL

            select configuracoes.fc_auditoria_cria_funcao('issqn.histriscoinscr');

            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;

            SELECT fc_gera_dicionario_apartir_tabela('issqn', 'histriscoinscr');

            COMMENT ON TABLE issqn.histriscoinscr IS
            '{
                "descricao": "Histórico risco de inscricões",
                "sigla": "q201",
                "dataincl": "2024-08-19",
                "rotulo": "histriscoinscr",
                "tipotabela": 2,
                "naolibclass": false,
                "naolibfunc": false,
                "naolibprog": false,
                "naolibform": false
            }';

            COMMENT ON COLUMN issqn.histriscoinscr.q201_sequencial IS
            '{
                "descricao": "Sequencial da tabela",
                "rotulo": "Sequencial Risco",
                "rotulorel": "Sequencial Risco",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 1,
                "tamanho": 10,
                "tipoobj": "text"
            }';

            COMMENT ON COLUMN issqn.histriscoinscr.q201_inscr IS
            '{
                "descricao": "Inscricão do imovel",
                "rotulo": "Cód. Inscrição",
                "rotulorel": "Cód. Inscrição",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 1,
                "tamanho": 10,
                "tipoobj": "text"
            }';

            COMMENT ON COLUMN issqn.histriscoinscr.q201_risco IS
            '{
                "descricao": "Risco da inscricão",
                "rotulo": "Risco inscricão",
                "rotulorel": "Risco inscricão",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 2,
                "tamanho": 10,
                "tipoobj": "text"
            }';

            COMMENT ON COLUMN issqn.histriscoinscr.q201_dtrisco IS
            '{
                "descricao": "Data de cadastro do risco da inscricão",
                "rotulo": "Data Risco inscricão",
                "rotulorel": "Data Risco inscricão",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 0,
                "tamanho": 10,
                "tipoobj": "text"
            }';

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
        select configuracoes.fc_auditoria_remove_funcao('issqn.histriscoinscr');
SQL
    );
        Schema::dropIfExists('issqn.histriscoinscr');
    }
}
