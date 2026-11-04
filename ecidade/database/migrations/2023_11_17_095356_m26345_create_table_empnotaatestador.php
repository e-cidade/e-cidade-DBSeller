<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class M26345CreateTableEmpnotaatestador extends Migration
{
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
        Schema::create('empenho.empnotaatestador', function (Blueprint $table) {
            $table->increments('e169_sequencial');
            $table->unsignedInteger('e169_empnota');
            $table->unsignedInteger('e169_numcgm');

            $table->foreign('e169_empnota')->references('e69_codnota')->on('empenho.empnota');
            $table->foreign('e169_numcgm')->references('z01_numcgm')->on('protocolo.cgm');
        });

        // comentarios da tabela
        $this->upComentarios();

        // auditoria
        DB::statement('SELECT public.fc_set_pg_search_path();');
        DB::statement("SELECT configuracoes.fc_auditoria_cria_funcao('empenho.empnotaatestador');");

        // disable triggers
        DB::statement('ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl DISABLE;');
        DB::statement('ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop DISABLE;');
    }

    private function upComentarios()
    {
        DB::connection()->getPdo()->exec(<<<SQL

        COMMENT ON TABLE empenho.empnotaatestador IS
            '{
                "descricao": "Atestadores da nota fiscal",
                "sigla": "e169",
                "dataincl": "2023-11-17",
                "rotulo": "Tabela",
                "tipotabela": 0,
                "naolibclass": false,
                "naolibfunc": false,
                "naolibprog": false,
                "naolibform": false
            }'
        ;

        COMMENT ON COLUMN empenho.empnotaatestador.e169_sequencial IS
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

        COMMENT ON COLUMN empenho.empnotaatestador.e169_empnota IS
            '{
                "descricao": "Cod da empanota",
                "rotulo": "Cod da empanota",
                "rotulorel": "Cod da empanota",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 1,
                "tamanho": 10,
                "tipoobj": "text"
            }'
        ;

        COMMENT ON COLUMN empenho.empnotaatestador.e169_numcgm IS
            '{
                "descricao": "Cod do cgm",
                "rotulo": "Cod do cgm",
                "rotulorel": "Cod do cgm",
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
        Schema::drop('empenho.empnotaatestador');
    }
}