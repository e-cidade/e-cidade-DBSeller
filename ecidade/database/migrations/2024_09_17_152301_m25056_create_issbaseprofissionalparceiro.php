<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M25056CreateIssbaseprofissionalparceiro extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        DB::unprepared(<<<SQL
            CREATE TABLE issqn.issbaseprofissionalparceiro (
                q203_sequencial SERIAL NOT NULL,
                q203_inscrsalao INTEGER,
                q203_inscrparc INTEGER,
                q203_numcgm INTEGER,
                q203_dtinicial date,
                q203_dtfinal date,
                q203_obs varchar(300),
                q202_sequencial INTEGER,
                PRIMARY KEY (q203_sequencial),
                FOREIGN KEY (q203_inscrsalao) REFERENCES issqn.issbase(q02_inscr),
                FOREIGN KEY (q203_inscrparc) REFERENCES issqn.issbase(q02_inscr),
                FOREIGN KEY (q202_sequencial) REFERENCES issqn.issbasesalaoparceiro(q202_sequencial),
                FOREIGN KEY (q203_numcgm) REFERENCES protocolo.cgm(z01_numcgm)

            );
SQL
        );

        DB::unprepared(<<<SQL

            select configuracoes.fc_auditoria_cria_funcao('issqn.issbaseprofissionalparceiro');

            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;

            SELECT fc_gera_dicionario_apartir_tabela('issqn', 'issbaseprofissionalparceiro');

            COMMENT ON TABLE issqn.issbaseprofissionalparceiro IS
            '{
                "descricao": "Parceiros",
                "sigla": "q203",
                "dataincl": "2024-09-17",
                "rotulo": "issbaseprofissionalparceiro",
                "tipotabela": 2,
                "naolibclass": false,
                "naolibfunc": false,
                "naolibprog": false,
                "naolibform": false
            }';

            COMMENT ON COLUMN issqn.issbaseprofissionalparceiro.q203_sequencial IS
            '{
                "descricao": "Sequencial da tabela",
                "rotulo": "Sequencial Parceiros",
                "rotulorel": "Sequencial Parceiros",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 1,
                "tamanho": 10,
                "tipoobj": "text"
            }';

            COMMENT ON COLUMN issqn.issbaseprofissionalparceiro.q203_inscrsalao IS
            '{
                "descricao": "Inscriчуo Salуo",
                "rotulo": "Inscr. Salуo",
                "rotulorel": "Inscr. Salуo",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 1,
                "tamanho": 10,
                "tipoobj": "text"
            }';

            COMMENT ON COLUMN issqn.issbaseprofissionalparceiro.q203_inscrparc IS
            '{
                "descricao": "Inscriчуo Parceiro",
                "rotulo": "Inscr. Parceiro",
                "rotulorel": "Inscr. Parceiro",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 1,
                "tamanho": 10,
                "tipoobj": "text"
            }';

            COMMENT ON COLUMN issqn.issbaseprofissionalparceiro.q203_numcgm IS
            '{
                "descricao": "CGM Parceiro",
                "rotulo": "CGM Parceiro",
                "rotulorel": "CGM Parceiro",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 1,
                "tamanho": 10,
                "tipoobj": "text"
            }';

            COMMENT ON COLUMN issqn.issbaseprofissionalparceiro.q203_dtinicial IS 
            '{
                "descricao": "Data Inicial Parceria",
                "rotulo": "Data Inicial Parceria",
                "rotulorel": "Data Inicial Parceria",
                "maiusculo": false,
                "autocompl": true,
                "aceitatipo": 0,
                "tamanho": 10,
                "tipoobj": "text"
            }';

            COMMENT ON COLUMN issqn.issbaseprofissionalparceiro.q203_dtfinal IS 
            '{
                "descricao": "Data Final Parceria",
                "rotulo": "Data Final Parceria",
                "rotulorel": "Data Final Parceria",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 0,
                "tamanho": 10,
                "tipoobj": "text"
            }';

            COMMENT ON COLUMN issqn.issbaseprofissionalparceiro.q203_obs IS 
            '{
                "descricao": "Observaчуo Parceria",
                "rotulo": "Observaчуo Parceria",
                "rotulorel": "Observaчуo Parceria",
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
        select configuracoes.fc_auditoria_remove_funcao('issqn.issbaseprofissionalparceiro');
SQL
    );
        Schema::dropIfExists('issqn.issbaseprofissionalparceiro');
    }
}
