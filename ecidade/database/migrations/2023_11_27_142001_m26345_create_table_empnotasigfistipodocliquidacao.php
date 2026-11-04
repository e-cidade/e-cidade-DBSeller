<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class M26345CreateTableEmpnotasigfistipodocliquidacao extends Migration
{
    private $tableName = 'empenho.empnotasigfistipodocliquidacao';

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

        // estrutura tabela
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->increments('e178_sequencial');
            $table->unsignedInteger('e178_empnota');
            $table->unsignedInteger('e178_sigfistipodocliquidacao');

            $table->foreign('e178_empnota')->references('e69_codnota')->on('empenho.empnota');
            $table->foreign('e178_sigfistipodocliquidacao')->references('e177_sequencial')->on('empenho.sigfistipodocliquidacao');
            $table->unique('e178_empnota');
        });

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

        COMMENT ON TABLE empenho.empnotasigfistipodocliquidacao IS
            '{
                "descricao": "Relacao Tipo documento de liquidacao do SIGFIS-RJ com a nota do e-cidade",
                "sigla": "e178",
                "dataincl": "2023-11-27",
                "rotulo": "Tabela",
                "tipotabela": 0,
                "naolibclass": false,
                "naolibfunc": false,
                "naolibprog": false,
                "naolibform": false
            }'
        ;

        COMMENT ON COLUMN empenho.empnotasigfistipodocliquidacao.e178_sequencial IS
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

        COMMENT ON COLUMN empenho.empnotasigfistipodocliquidacao.e178_empnota IS
            '{
                "descricao": "Codigo da nota",
                "rotulo": "Codigo da nota",
                "rotulorel": "Codigo da nota",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 1,
                "tamanho": 10,
                "tipoobj": "text"
            }'
        ;

        COMMENT ON COLUMN empenho.empnotasigfistipodocliquidacao.e178_sigfistipodocliquidacao IS
            '{
                "descricao": "Sequencial do documento",
                "rotulo": "Sequencial do documento",
                "rotulorel": "Sequencial do documento",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 1,
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
