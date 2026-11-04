<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class M26817CreateTableConplanoexeempenho extends Migration
{
    private $tableName = 'contabilidade.conplanoexeempenho';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // enable triggers
        DB::statement('ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;');
        DB::statement('ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;');

        // estrutura
        DB::connection()->getPdo()->exec(<<<SQL

        CREATE TABLE {$this->tableName} (
            c151_codigo SERIAL PRIMARY KEY,
            c151_exercicio INTEGER NOT NULL,
            c151_reduzido  INTEGER NOT NULL,
            c151_empenho INTEGER NOT NULL,
            c151_valor NUMERIC(17, 2) NOT NULL,
            c151_natureza CHAR(1) NOT NULL,

            UNIQUE (c151_exercicio, c151_empenho, c151_reduzido),
            FOREIGN KEY (c151_reduzido, c151_exercicio) REFERENCES contabilidade.conplanoreduz(c61_reduz, c61_anousu),
            FOREIGN KEY (c151_empenho) REFERENCES empenho.empempenho(e60_numemp)
        )
SQL
        );

        // comentarios da tabela
        $this->upComentarios();

        // auditoria
        DB::statement('SELECT public.fc_set_pg_search_path();');
        DB::statement("SELECT configuracoes.fc_auditoria_cria_funcao('{$this->tableName}');");

        // disable triggers
        DB::statement('ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl DISABLE;');
        DB::statement('ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop DISABLE;');
    }


    private function upComentarios()
    {
        DB::connection()->getPdo()->exec(<<<SQL

        COMMENT ON TABLE {$this->tableName} IS
            '{
                "descricao": "Conplano Exercicio Empenhos",
                "sigla": "c151",
                "dataincl": "2024-01-08",
                "rotulo": "Tabela",
                "tipotabela": 0,
                "naolibclass": false,
                "naolibfunc": false,
                "naolibprog": false,
                "naolibform": false
            }'
        ;

        COMMENT ON COLUMN {$this->tableName}.c151_codigo IS
            '{
                "descricao": "Chave primária da tabela",
                "rotulo": "Código",
                "rotulorel": "Código",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 1,
                "tamanho": 10,
                "tipoobj": "text"
            }'
        ;

        COMMENT ON COLUMN {$this->tableName}.c151_reduzido IS
            '{
                "descricao": "Codigo do reduzido",
                "rotulo": "Codigo do reduzido",
                "rotulorel": "Codigo do reduzido",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 1,
                "tamanho": 10,
                "tipoobj": "text"
            }'
        ;

        COMMENT ON COLUMN {$this->tableName}.c151_empenho IS
            '{
                "descricao": "Sequencila do Empenho",
                "rotulo": "Sequencila do Empenho",
                "rotulorel": "Sequencila do Empenho",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 1,
                "tamanho": 10,
                "tipoobj": "text"
            }'
        ;

        COMMENT ON COLUMN {$this->tableName}.c151_valor IS
            '{
                "descricao": "Valor",
                "rotulo": "Valor",
                "rotulorel": "Valor",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 1,
                "tamanho": 10,
                "tipoobj": "checkbox"
            }'
        ;

        COMMENT ON COLUMN {$this->tableName}.c151_natureza IS
            '{
                "descricao": "Natureza",
                "rotulo": "Natureza",
                "rotulorel": "Natureza",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 2,
                "tamanho": 10,
                "tipoobj": "text"
            }'
        ;
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
        Schema::drop($this->tableName);
    }
}
