<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M26220AdicionaTabelaVinculoMenorAprendiz extends Migration
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
                CREATE SEQUENCE pessoal.rhvinculoaprendiz_id_seq
                INCREMENT 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1;
        
                create table IF NOT EXISTS pessoal.rhvinculoaprendiz (            
                    rh312_sequencial int4 NOT NULL DEFAULT nextval('pessoal.rhvinculoaprendiz_id_seq'),
                    rh312_matricula int4 not null,
                    rh312_instit int4 not null,
                    rh312_modalidade int4 not null,
                    rh312_cnpjqualificadora varchar(14) default '',
                    rh312_cnpjefetivada varchar(14) default '',
                    rh312_cnpjpratica varchar(14) default '',
                    CONSTRAINT rh312_rhvinculoaprendiz_id_pk PRIMARY KEY (rh312_sequencial),
                    constraint rh312_rhvinculoaprendiz_fk foreign key (rh312_matricula) references pessoal.rhpessoal(rh01_regist)
                );
                ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;
                ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;

                COMMENT ON TABLE pessoal.rhvinculoaprendiz IS
                '{
                    "descricao": "Armazena os dados de cadastro de menor aprendiz para o eSocial",
                    "sigla": "rh312",
                    "dataincl": "2023-11-28",
                    "rotulo": "rhvinculoaprendiz",
                    "tipotabela": "0",
                    "naolibclass": "false",
                    "naolibfunc": "false",
                    "naolibprog": "false",
                    "naolibform": "false"
                }';

                COMMENT ON COLUMN pessoal.rhvinculoaprendiz.rh312_sequencial IS
                '{
                    "descricao": "Código Sequencial",
                    "rotulo": "Código Sequencial",
                    "rotulorel": "Código Sequencial",
                    "maiusculo": false,
                    "autocompl": false,
                    "aceitatipo": 1,
                    "tamanho": 10,
                    "tipoobj": "text"
                }';

                COMMENT ON COLUMN pessoal.rhvinculoaprendiz.rh312_matricula IS
                '{
                    "descricao": "Matrícula",
                    "rotulo": "Matrícula do Servidor",
                    "rotulorel": "Matrícula do Servidor",
                    "maiusculo": false,
                    "autocompl": false,
                    "aceitatipo": 1,
                    "tamanho": 10,
                    "tipoobj": "text"
                }';

                COMMENT ON COLUMN pessoal.rhvinculoaprendiz.rh312_modalidade IS
                '{
                    "descricao": "Modalidade de Contratação do Menor Aprendiz",
                    "rotulo": "Modalidade de Contratação",
                    "rotulorel": "Modalidade de Contratação",
                    "maiusculo": false,
                    "autocompl": false,
                    "aceitatipo": 1,
                    "tamanho": 10,
                    "tipoobj": "text"
                }';
        
                COMMENT ON COLUMN pessoal.rhvinculoaprendiz.rh312_instit IS
                '{
                    "descricao": "Instituição",
                    "rotulo": "Instituição",
                    "rotulorel": "Instituição",
                    "maiusculo": false,
                    "autocompl": false,
                    "aceitatipo": 1,
                    "tamanho": 10,
                    "tipoobj": "text"
                }';

                COMMENT ON COLUMN pessoal.rhvinculoaprendiz.rh312_cnpjqualificadora IS
                '{
                    "descricao": "CNPJ da Entidade Qualificadora",
                    "rotulo": "CNPJ da Entidade Qualificadora",
                    "rotulorel": "CNPJ da Entidade Qualificadora",
                    "maiusculo": false,
                    "autocompl": false,
                    "aceitatipo": 3,
                    "tamanho": 14,
                    "tipoobj": "text"
                }';

                COMMENT ON COLUMN pessoal.rhvinculoaprendiz.rh312_cnpjefetivada IS
                '{
                    "descricao": "CNPJ do Estabelecimento ",
                    "rotulo": "Ano de Envio",
                    "rotulorel": "Ano de Envio",
                    "maiusculo": false,
                    "autocompl": false,
                    "aceitatipo": 3,
                    "tamanho": 14,
                    "tipoobj": "text"
                }';
         
                COMMENT ON COLUMN pessoal.rhvinculoaprendiz.rh312_cnpjpratica IS
                '{
                    "descricao": "Ano de Envio",
                    "rotulo": "Ano de Envio",
                    "rotulorel": "Ano de Envio",
                    "maiusculo": false,
                    "autocompl": false,
                    "aceitatipo": 3,
                    "tamanho": 14,
                    "tipoobj": "text"
                }';

                select fc_gera_dicionario_apartir_tabela('pessoal', 'rhvinculoaprendiz');                                                            
                ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl DISABLE;
                ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop DISABLE;

                select configuracoes.fc_auditoria_cria_funcao('pessoal.rhvinculoaprendiz');
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
        $sql = <<<SQL
        select fc_remove_dicionario_tabela('pessoal', 'rhvinculoaprendiz');
        select configuracoes.fc_auditoria_remove_funcao('pessoal.rhvinculoaprendiz');
        DROP TABLE pessoal.rhvinculoaprendiz;
        DROP SEQUENCE pessoal.rhvinculoaprendiz_id_seq;
SQL;
        DB::connection()->getPdo()->exec($sql);
    }
}
