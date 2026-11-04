<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIssbasededucaosalao extends Migration
{
/**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        DB::unprepared(<<<SQL
            CREATE TABLE issqn.issbasededucaosalao (
                q204_sequencial SERIAL NOT NULL,
                q204_inscr INTEGER NOT NULL,
                q204_estrutural VARCHAR NOT NULL,
                q204_deducao FLOAT,
                PRIMARY KEY (q204_sequencial),
                FOREIGN KEY (q204_inscr) REFERENCES issqn.issbase(q02_inscr)
            );
SQL
        );
        DB::unprepared(<<<SQL

            select configuracoes.fc_auditoria_cria_funcao('issqn.issbasededucaosalao');

            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;

            SELECT fc_gera_dicionario_apartir_tabela('issqn', 'issbasededucaosalao');

            COMMENT ON TABLE issqn.issbasededucaosalao IS
            '{
                "descricao": "Dedъcaзгo para Atividades Salгo Parceiro",
                "sigla": "q204",
                "dataincl": "2024-09-23",
                "rotulo": "issbasededucaosalao",
                "tipotabela": 2,
                "naolibclass": false,
                "naolibfunc": false,
                "naolibprog": false,
                "naolibform": false
            }';

            COMMENT ON COLUMN issqn.issbasededucaosalao.q204_sequencial IS
            '{
                "descricao": "Sequencial da tabela",
                "rotulo": "Sequencial Deduзгo",
                "rotulorel": "Sequencial Deduзгo",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 1,
                "tamanho": 10,
                "tipoobj": "text"
            }';

            COMMENT ON COLUMN issqn.issbasededucaosalao.q204_inscr IS
            '{
                "descricao": "Inscricгo do Salao Parceiro",
                "rotulo": "Cуd. Inscriзгo",
                "rotulorel": "Cуd. Inscriзгo",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 1,
                "tamanho": 10,
                "tipoobj": "text"
            }';

            COMMENT ON COLUMN issqn.issbasededucaosalao.q204_estrutural IS
            '{
                "descricao": "CNAE Atividade Salгo Parceiro",
                "rotulo": "Cуd. CNAE",
                "rotulorel": "Cуd. CNAE",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 3,
                "tamanho": 10,
                "tipoobj": "text"
            }';

            COMMENT ON COLUMN issqn.issbasededucaosalao.q204_deducao IS 
            '{
              "descricao": "Deduзгo Mбxima da Atividade",
              "rotulo": "Deduзгo Mбxima",
              "rotulorel": "Deduзгo Mбxima da Atividade",
              "maiusculo": false,
              "autocompl": false,
              "aceitatipo": 4,
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
        select configuracoes.fc_auditoria_remove_funcao('issqn.issbasededucaosalao');
SQL
    );
        Schema::dropIfExists('issqn.issbasededucaosalao');
    }
}
