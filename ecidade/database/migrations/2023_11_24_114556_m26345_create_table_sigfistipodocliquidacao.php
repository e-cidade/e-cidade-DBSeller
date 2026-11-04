<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class M26345CreateTableSigfistipodocliquidacao extends Migration
{
    private $tableName = 'empenho.sigfistipodocliquidacao';

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
            $table->increments('e177_sequencial');
            $table->unsignedSmallInteger('e177_codigo');
            $table->string('e177_descr', 50);
            $table->unsignedSmallInteger('e177_tipodiverso')->nullable();
            $table->boolean('e177_decimo')->nullable();
        });

        // Dados
        DB::table($this->tableName)->insert([
            [
                'e177_codigo'      => 1,
                'e177_descr'       => 'Nota Fiscal',
                'e177_decimo'      => null,
                'e177_tipodiverso' => null
            ],
            [
                'e177_codigo'      => 2,
                'e177_descr'       => 'Folha de Pagamento',
                'e177_decimo'      => false,
                'e177_tipodiverso' => null
            ],
            [
                'e177_codigo'       => 2,
                'e177_descr'        => 'Folha de Pagamento - Décimo terceiro',
                'e177_decimo'       => true,
                'e177_tipodiverso'  => null
            ],
            [
                'e177_codigo'       => 3,
                'e177_descr'        => 'Diversos - Recibo',
                'e177_tipodiverso'  => 1,
                'e177_decimo'       => null
            ],
            [
                'e177_codigo'       => 3,
                'e177_descr'        => 'Diversos - Guia de Pagamento',
                'e177_tipodiverso'  => 2,
                'e177_decimo'       => null
            ],
            [
                'e177_codigo'       => 4,
                'e177_descr'        => 'Diárias',
                'e177_tipodiverso'  => null,
                'e177_decimo'       => null
            ],
            [
                'e177_codigo'       => 5,
                'e177_descr'        => 'Adiantamento',
                'e177_tipodiverso'  => null,
                'e177_decimo'       => null
            ]
        ]);

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

        COMMENT ON TABLE empenho.sigfistipodocliquidacao IS
            '{
                "descricao": "Tipo documento de liquidacao do SIGFIS-RJ",
                "sigla": "e177",
                "dataincl": "2023-11-24",
                "rotulo": "Tabela",
                "tipotabela": 0,
                "naolibclass": false,
                "naolibfunc": false,
                "naolibprog": false,
                "naolibform": false
            }'
        ;

        COMMENT ON COLUMN empenho.sigfistipodocliquidacao.e177_sequencial IS
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

        COMMENT ON COLUMN empenho.sigfistipodocliquidacao.e177_codigo IS
            '{
                "descricao": "Codigo do documento",
                "rotulo": "Codigo do documento",
                "rotulorel": "Codigo do documento",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 1,
                "tamanho": 10,
                "tipoobj": "text"
            }'
        ;

        COMMENT ON COLUMN empenho.sigfistipodocliquidacao.e177_descr IS
            '{
                "descricao": "Descricao",
                "rotulo": "Descricao",
                "rotulorel": "Descricao",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 3,
                "tamanho": 10,
                "tipoobj": "text"
            }'
        ;

        COMMENT ON COLUMN empenho.sigfistipodocliquidacao.e177_decimo IS
            '{
                "descricao": "Se o tipo folha é decimo terceiro",
                "rotulo": "Decimo terceiro",
                "rotulorel": "Decimo terceiro",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 5,
                "tamanho": 10,
                "tipoobj": "text"
            }'
        ;

        COMMENT ON COLUMN empenho.sigfistipodocliquidacao.e177_tipodiverso IS
            '{
                "descricao": "Tipo do documento diverso: 1 - Recibo, 2 - Guia de pagamentp",
                "rotulo": "Tipo documento diverso",
                "rotulorel": "Tipo documento diverso",
                "maiusculo": false,
                "autocompl": false,
                "aceitatipo": 3,
                "tamanho": 1,
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
