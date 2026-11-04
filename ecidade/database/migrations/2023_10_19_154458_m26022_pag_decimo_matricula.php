<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class M26022PagDecimoMatricula extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        DB::connection()->getPdo()->exec(
            <<<SQL
        CREATE SEQUENCE pessoal.pagservidordecimo_id_seq
           INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;
 
        create table IF NOT EXISTS pessoal.servidordecimo (
       
           rh311_sequencial        int4 NOT NULL DEFAULT nextval('pessoal.pagservidordecimo_id_seq'),
           rh311_matricula int4 not null,
           rh311_instit    smallint not null,
           rh311_data      date not null,
           rh311_ano       smallint not null,
           CONSTRAINT rh311_servidordecimo_id_pk PRIMARY KEY (rh311_sequencial),
           constraint rh311_servidordecimo_fk foreign key (rh311_matricula) references pessoal.rhpessoal(rh01_regist)
        );
        ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;
        ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;
        COMMENT ON TABLE pessoal.servidordecimo IS '{"descricao": "Armazena matriculas que enviaram para o eSocial o decimo terceiro individualmente",
                                                   "sigla": "rh311",
                                                   "dataincl": "2023-10-19",
                                                   "rotulo": "servidordecimo",
                                                   "tipotabela": "0",
                                                   "naolibclass": "false",
                                                   "naolibfunc": "false",
                                                   "naolibprog": "false",
                                                   "naolibform": "false"
                                                  }';
        COMMENT ON COLUMN pessoal.servidordecimo.rh311_matricula IS '{"descricao": "Código do Pagamento",
                                                             "rotulo": "Código do Pagamento",
                                                             "rotulorel": "Código do Pagamento",
                                                             "maiusculo": false,
                                                             "autocompl": false,
                                                             "aceitatipo": 1,
                                                             "tamanho": 10,
                                                             "tipoobj": "text"
                                                           }';
        COMMENT ON COLUMN pessoal.servidordecimo.rh311_instit IS '{"descricao": "Instituição",
                                                                    "rotulo": "Instituição",
                                                                    "rotulorel": "Instituição",
                                                                    "maiusculo": false,
                                                                    "autocompl": false,
                                                                    "aceitatipo": 3,
                                                                    "tamanho": 100,
                                                                    "tipoobj": "text"
                                                                  }';
        COMMENT ON COLUMN pessoal.servidordecimo.rh311_data IS '{"descricao": "Data de Envio",
                                                                    "rotulo": "Data de Envio",
                                                                    "rotulorel": "Data de Envio",
                                                                    "maiusculo": false,
                                                                    "autocompl": false,
                                                                    "aceitatipo": 3,
                                                                    "tamanho": 100,
                                                                    "tipoobj": "text"
                                                                  }' ;
        COMMENT ON COLUMN pessoal.servidordecimo.rh311_ano IS '{"descricao": "Ano de Envio",
                                                                    "rotulo": "Ano de Envio",
                                                                    "rotulorel": "Ano de Envio",
                                                                    "maiusculo": false,
                                                                    "autocompl": false,
                                                                    "aceitatipo": 3,
                                                                    "tamanho": 100,
                                                                    "tipoobj": "text"
                                                                  }';
        select  fc_gera_dicionario_apartir_tabela('pessoal', 'servidordecimo');                                                            
        ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl DISABLE;
        ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop DISABLE;

        select configuracoes.fc_auditoria_cria_funcao('pessoal.servidordecimo');
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
        DB::connection()->getPdo()->exec(
            <<<SQL
            select fc_remove_dicionario_tabela('pessoal', 'servidordecimo');
            DROP SEQUENCE pessoal.pagservidordecimo_id_seq CASCADE;
            select configuracoes.fc_auditoria_remove_funcao('pessoal.servidordecimo ');
            DROP  TABLE IF EXISTS pessoal.servidordecimo;

SQL
        );
    }
}
