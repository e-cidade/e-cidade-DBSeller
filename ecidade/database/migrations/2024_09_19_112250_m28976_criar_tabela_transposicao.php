<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M28976CriarTabelaTransposicao extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->criaEstrutura();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->dropaEstrutura();
    }

    private function criaEstrutura()
    {
        DB::statement("
            CREATE TABLE IF NOT EXISTS escola.transposicao (
                ed353_codigo BIGSERIAL PRIMARY KEY,
                ed353_ano INTEGER NOT NULL,
                ed353_ativa BOOLEAN,
                ed353_processada BOOLEAN
            )
        ");

        // Habilitando triggers
        DB::statement("ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;");
        DB::statement("ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;");

        // Adicionando comentários à tabela
        DB::statement("COMMENT ON TABLE escola.transposicao IS '{
            \"descricao\": \"Armazena as informações dos alunos designados que fizeram a transposição.\",
            \"sigla\": \"ed353\",
            \"dataincl\": \"2024-09-19\",
            \"rotulo\": \"transposicao\",
            \"tipotabela\": 0,
            \"naolibclass\": false,
            \"naolibfunc\": false,
            \"naolibprog\": false,
            \"naolibform\": false
        }'");

        // Adicionando comentários às colunas
        DB::statement("COMMENT ON COLUMN escola.transposicao.ed353_codigo IS '{
            \"descricao\": \"Chave primária da tabela\",
            \"rotulo\": \"ed353_codigo\",
            \"rotulorel\": \"ed353_codigo\",
            \"maiusculo\": false,
            \"autocompl\": false,
            \"aceitatipo\": 1,
            \"tamanho\": 18,
            \"tipoobj\": \"text\"
        }'");

        DB::statement("COMMENT ON COLUMN escola.transposicao.ed353_ano IS '{
            \"descricao\": \"Coluna que armazena o ano da transposicao\",
            \"rotulo\": \"ed353_ano\",
            \"rotulorel\": \"ed353_ano\",
            \"maiusculo\": false,
            \"autocompl\": false,
            \"aceitatipo\": 1,
            \"tamanho\": 18,
            \"tipoobj\": \"text\"
        }'");

        DB::statement("COMMENT ON COLUMN escola.transposicao.ed353_ativa IS '{
            \"descricao\": \"Armazena o estado da transposicao (Ativo ou Inativo)\",
            \"rotulo\": \"ed353_ativa\",
            \"rotulorel\": \"ed353_ativa\",
            \"maiusculo\": false,
            \"autocompl\": false,
            \"aceitatipo\": 5,
            \"tamanho\": 18,
            \"tipoobj\": \"checkbox\"
        }'");

        DB::statement("COMMENT ON COLUMN escola.transposicao.ed353_processada IS '{
            \"descricao\": \"Armazena se a transposicao foi processada ou não\",
            \"rotulo\": \"ed353_processada\",
            \"rotulorel\": \"ed353_processada\",
            \"maiusculo\": false,
            \"autocompl\": false,
            \"aceitatipo\": 5,
            \"tamanho\": 18,
            \"tipoobj\": \"checkbox\"
        }'");

        DB::select("SELECT fc_gera_dicionario_apartir_tabela(?, ?)", ['escola', 'transposicao']);

        DB::statement("ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl DISABLE;");
        DB::statement("ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop DISABLE;");
    }

    private function dropaEstrutura()
    {
        Schema::dropIfExists('escola.transposicao');
    }
}
