<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M23511CreateTableEfdreabfechdadosresp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<SQL

            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;

            CREATE TABLE esocial.efdreabfechdadosresp (
	            efd10_sequencial serial4 NOT NULL,
	            efd10_nome varchar NOT NULL,
	            efd10_cpf varchar NOT NULL,
	            efd10_telefone varchar NOT NULL,
	            efd10_email varchar NOT NULL,
                efd10_numcgm int4 NOT NULL,
	            CONSTRAINT efdreabfechdadosresp_pk PRIMARY KEY (efd10_sequencial),
                CONSTRAINT efdreabfechdadosresp_fk FOREIGN KEY (efd10_numcgm) REFERENCES protocolo.cgm(z01_numcgm)
            );

            CREATE UNIQUE INDEX efdreabfechdadosresp_efd10_numcgm_idx ON esocial.efdreabfechdadosresp USING btree (efd10_numcgm);

            COMMENT ON TABLE esocial.efdreabfechdadosresp IS '
            {
                "descricao": "Tabela dos dados responsaveis pelo r4099",
                "sigla": "efd10",
                "dataincl": "2023-09-24",
                "rotulo": "efdreabfechdadosresp",
                "tipotabela": 0,
                "naolibclass": false,
                "naolibfunc": false,
                "naolibprog": false,
                "naolibform": false
            }';

            COMMENT ON COLUMN esocial.efdreabfechdadosresp.efd10_sequencial IS '
            {
                "descricao": "Chave primária da tabela",
                "rotulo": "efd10_sequencial",
                "rotulorel": "efd10_sequencial",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 1,
                "tamanho": 10,
                "tipoobj": "text"
            }';

            COMMENT ON COLUMN esocial.efdreabfechdadosresp.efd10_nome IS '
            {
                "descricao": "Nome do responsavel",
                "rotulo": "efd10_nome",
                "rotulorel": "efd10_nome",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 3,
                "tamanho": 50,
                "tipoobj": "text"
            }';

            COMMENT ON COLUMN esocial.efdreabfechdadosresp.efd10_cpf IS '
            {
                "descricao": "Cpf do responsavel",
                "rotulo": "efd10_cpf",
                "rotulorel": "efd10_cpf",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 3,
                "tamanho": 50,
                "tipoobj": "text"
            }';

            COMMENT ON COLUMN esocial.efdreabfechdadosresp.efd10_telefone IS '
            {
                "descricao": "Telefone do responsavel",
                "rotulo": "efd10_telefone",
                "rotulorel": "efd10_telefone",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 3,
                "tamanho": 50,
                "tipoobj": "text"
            }';

            COMMENT ON COLUMN esocial.efdreabfechdadosresp.efd10_email IS '
            {
                "descricao": "Email do responsavel",
                "rotulo": "efd10_email",
                "rotulorel": "efd10_email",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 3,
                "tamanho": 50,
                "tipoobj": "text"
            }';

            COMMENT ON COLUMN esocial.efdreabfechdadosresp.efd10_numcgm IS '
            {
                "descricao": "CGM do Contribuinte",
                "rotulo": "efd10_numcgm",
                "rotulorel": "efd10_numcgm",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 1,
                "tamanho": 10,
                "tipoobj": "text"
            }';

            -- auditoria / dicionario
            SELECT configuracoes.fc_auditoria_cria_funcao('esocial.efdreabfechdadosresp');
            SELECT fc_gera_dicionario_apartir_tabela('esocial', 'efdreabfechdadosresp');

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
        DB::statement("DROP TABLE esocial.efdreabfechdadosresp");
    }
}
