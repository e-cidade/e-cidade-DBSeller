<?php

use Illuminate\Database\Migrations\Migration;

class M29329CreateTableVinculoreceitaprocedencia extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(<<<SQL
            CREATE TABLE caixa.vinculoreceitaprocedencia (
                k202_sequencial serial PRIMARY KEY,
                k202_receita integer NOT NULL,
                k202_procdiver integer,
                k202_proced integer,
                FOREIGN KEY(k202_receita) REFERENCES caixa.tabrec(k02_codigo) ON DELETE CASCADE,
                FOREIGN KEY(k202_procdiver) REFERENCES diversos.procdiver(dv09_procdiver) ON DELETE CASCADE,
                FOREIGN KEY(k202_proced) REFERENCES divida.proced(v03_codigo) ON DELETE CASCADE,
                UNIQUE(k202_receita, k202_procdiver),
                UNIQUE(k202_receita, k202_proced)
            );

            COMMENT ON TABLE caixa.vinculoreceitaprocedencia IS
            '{
                "descricao": "Vinculo de Receita e Procedencia",
                "sigla": "k202",
                "dataincl": "2024-11-28",
                "rotulo": "vinculoreceitaprocedencia",
                "tipotabela": 0,
                "naolibclass": false,
                "naolibfunc": false,
                "naolibprog": false,
                "naolibform": false
            }';

            COMMENT ON COLUMN caixa.vinculoreceitaprocedencia.k202_sequencial IS
            '{
                "descricao": "Identificador",
                "rotulo": "sequencial",
                "rotulorel": "Id",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 0,
                "tamanho": 10,
                "tipoobj": "text"
            }';

            COMMENT ON COLUMN caixa.vinculoreceitaprocedencia.k202_receita IS
            '{
                "descricao": "Receita",
                "rotulo": "Receita",
                "rotulorel": "Receita",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 1,
                "tamanho": 10,
                "tipoobj": "text"
            }';

            COMMENT ON COLUMN caixa.vinculoreceitaprocedencia.k202_procdiver IS
            '{
                "descricao": "Procedencia Diversos",
                "rotulo": "Procedencia",
                "rotulorel": "Procedencia",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 1,
                "tamanho": 10,
                "tipoobj": "text"
            }';

            COMMENT ON COLUMN caixa.vinculoreceitaprocedencia.k202_proced IS
            '{
                "descricao": "Procedencia Divida",
                "rotulo": "Procedencia",
                "rotulorel": "Procedencia",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 1,
                "tamanho": 10,
                "tipoobj": "text"
            }';

            -- audit
            SELECT configuracoes.fc_auditoria_cria_funcao('caixa.vinculoreceitaprocedencia');
            -- dicionario
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;
            SELECT fc_gera_dicionario_apartir_tabela('caixa', 'vinculoreceitaprocedencia');
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
        DB::unprepared("drop table caixa.vinculoreceitaprocedencia;");
    }
}
