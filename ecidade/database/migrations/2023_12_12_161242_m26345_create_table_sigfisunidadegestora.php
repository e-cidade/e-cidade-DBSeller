<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class M26345CreateTableSigfisunidadegestora extends Migration
{
    private $tableName = 'contabilidade.sigfisunidadegestora';

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
            $table->increments('c179_sequencial');
            $table->unsignedInteger('c179_codigo');
            $table->unsignedInteger('c179_instit');
            $table->boolean('c179_responsavelfolha')->default(true);
            $table->unsignedInteger('c179_codigofolha')->nullable();
            $table->unsignedInteger('c179_cgmordenadordespesa');

            $table->foreign('c179_instit')->references('codigo')->on('configuracoes.db_config');
            $table->foreign('c179_cgmordenadordespesa')->references('z01_numcgm')->on('protocolo.cgm');
            $table->unique('c179_codigo');
            $table->unique(['c179_codigo', 'c179_instit']);
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

        COMMENT ON TABLE contabilidade.sigfisunidadegestora IS
            '{
                "descricao": "Unidade Gestora SIGFIS-RJ",
                "sigla": "c179",
                "dataincl": "2023-12-12",
                "rotulo": "Tabela",
                "tipotabela": 0,
                "naolibclass": false,
                "naolibfunc": false,
                "naolibprog": false,
                "naolibform": false
            }'
        ;

        COMMENT ON COLUMN contabilidade.sigfisunidadegestora.c179_sequencial IS
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

        COMMENT ON COLUMN contabilidade.sigfisunidadegestora.c179_codigo IS
            '{
                "descricao": "Codigo da Unidade Gestora",
                "rotulo": "Codigo da Unidade Gestora",
                "rotulorel": "Codigo da Unidade Gestora",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 1,
                "tamanho": 10,
                "tipoobj": "text"
            }'
        ;

        COMMENT ON COLUMN contabilidade.sigfisunidadegestora.c179_codigofolha IS
            '{
                "descricao": "Codigo da Unidade Gestora Folha",
                "rotulo": "Codigo da Unidade Gestora Folha",
                "rotulorel": "Codigo da Unidade Gestora Folha",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 1,
                "tamanho": 10,
                "tipoobj": "text"
            }'
        ;

        COMMENT ON COLUMN contabilidade.sigfisunidadegestora.c179_responsavelfolha IS
            '{
                "descricao": "UG eh responsavel por folha",
                "rotulo": "UG eh responsavel por folha",
                "rotulorel": "UG eh responsavel por folha",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 5,
                "tamanho": 10,
                "tipoobj": "checkbox"
            }'
        ;

        COMMENT ON COLUMN contabilidade.sigfisunidadegestora.c179_instit IS
            '{
                "descricao": "Codigo da instituicao",
                "rotulo": "Codigo da instituicao",
                "rotulorel": "Codigo da instituicao",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 1,
                "tamanho": 10,
                "tipoobj": "text"
            }'
        ;

        COMMENT ON COLUMN contabilidade.sigfisunidadegestora.c179_cgmordenadordespesa IS
            '{
                "descricao": "Cgm do Ordenador de Despesa",
                "rotulo": "Cgm do Ordenador de Despesa",
                "rotulorel": "Cgm do Ordenador de Despesa",
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
