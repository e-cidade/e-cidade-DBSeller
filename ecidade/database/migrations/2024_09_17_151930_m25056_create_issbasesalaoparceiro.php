<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M25056CreateIssbasesalaoparceiro extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        DB::unprepared(<<<SQL
            CREATE TABLE issqn.issbasesalaoparceiro (
                q202_sequencial SERIAL NOT NULL,
                q202_inscr INTEGER,
                q202_dtinicial date,
                q202_dtfinal date,
                q202_obs varchar(300),
                PRIMARY KEY (q202_sequencial),
                FOREIGN KEY (q202_inscr) REFERENCES issqn.issbase(q02_inscr)
            );
SQL
        );
        DB::unprepared(<<<SQL

            select configuracoes.fc_auditoria_cria_funcao('issqn.issbasesalaoparceiro');

            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;

            SELECT fc_gera_dicionario_apartir_tabela('issqn', 'issbasesalaoparceiro');

            COMMENT ON TABLE issqn.issbasesalaoparceiro IS
            '{
                "descricao": "Salѕes Parceiros",
                "sigla": "q202",
                "dataincl": "2024-09-17",
                "rotulo": "issbasesalaoparceiro",
                "tipotabela": 2,
                "naolibclass": false,
                "naolibfunc": false,
                "naolibprog": false,
                "naolibform": false
            }';

            COMMENT ON COLUMN issqn.issbasesalaoparceiro.q202_sequencial IS
            '{
                "descricao": "Sequencial da tabela",
                "rotulo": "Sequencial Salуo",
                "rotulorel": "Sequencial Salуo",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 1,
                "tamanho": 10,
                "tipoobj": "text"
            }';

            COMMENT ON COLUMN issqn.issbasesalaoparceiro.q202_inscr IS
            '{
                "descricao": "Inscricуo do imovel",
                "rotulo": "Cѓd. Inscriчуo",
                "rotulorel": "Cѓd. Inscriчуo",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 1,
                "tamanho": 10,
                "tipoobj": "text"
            }';

            COMMENT ON COLUMN issqn.issbasesalaoparceiro.q202_dtinicial IS 
            '{
              "descricao": "Data Inicial Salуo Parceiro",
              "rotulo": "Data Inicial Salуo Parceiro",
              "rotulorel": "Data Inicial Salуo Parceiro",
              "maiusculo": false,
              "autocompl": true,
              "aceitatipo": 0,
              "tamanho": 10,
              "tipoobj": "text"
            }';

            COMMENT ON COLUMN issqn.issbasesalaoparceiro.q202_dtfinal IS 
            '{
              "descricao": "Data Final Salуo Parceiro",
              "rotulo": "Data Final Salуo Parceiro",
              "rotulorel": "Data Final Salуo Parceiro",
              "maiusculo": false,
              "autocompl": false,
              "aceitatipo": 0,
              "tamanho": 10,
              "tipoobj": "text"
            }';

            COMMENT ON COLUMN issqn.issbasesalaoparceiro.q202_obs IS 
            '{
              "descricao": "Observaчуo Salуo Parceiro",
              "rotulo": "Observaчуo Salуo Parceiro",
              "rotulorel": "Observaчуo Salуo Parceiro",
              "maiusculo": false,
              "autocompl": false,
              "aceitatipo": 0,
              "tamanho": 300,
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
        select configuracoes.fc_auditoria_remove_funcao('issqn.issbasesalaoparceiro');
SQL
    );
        Schema::dropIfExists('issqn.issbasesalaoparceiro');
    }
}
