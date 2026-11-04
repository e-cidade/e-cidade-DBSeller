<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M28976EstruturaTransposicao extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("
            CREATE TABLE IF NOT EXISTS escola.transposicao (
                ed353_codigo BIGSERIAL PRIMARY KEY,
                ed353_ano INTEGER NOT NULL,
                ed353_ativa BOOLEAN DEFAULT FALSE,
                ed353_processada BOOLEAN DEFAULT FALSE
            )
        ");

        // Habilitando triggers
        DB::statement("ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;");
        DB::statement("ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;");

        // Adicionando comentсrios р tabela
        DB::statement("COMMENT ON TABLE escola.transposicao IS '{
            \"descricao\": \"Armazena as informaчѕes da transposiчуo.\",
            \"sigla\": \"ed353\",
            \"dataincl\": \"2024-09-21\",
            \"rotulo\": \"transposicao\",
            \"tipotabela\": 0,
            \"naolibclass\": false,
            \"naolibfunc\": false,
            \"naolibprog\": false,
            \"naolibform\": false
        }'");

        // Adicionando comentсrios рs colunas
        DB::statement("COMMENT ON COLUMN escola.transposicao.ed353_codigo IS '{
            \"descricao\": \"Chave primсria da tabela\",
            \"rotulo\": \"ed353_codigo\",
            \"rotulorel\": \"ed353_codigo\",
            \"maiusculo\": false,
            \"autocompl\": false,
            \"aceitatipo\": 1,
            \"tamanho\": 18,
            \"tipoobj\": \"text\"
        }'");

        DB::statement("COMMENT ON COLUMN escola.transposicao.ed353_ano IS '{
            \"descricao\": \"Armazena o ano da transposiчуo\",
            \"rotulo\": \"ed353_ano\",
            \"rotulorel\": \"ed353_ano\",
            \"maiusculo\": false,
            \"autocompl\": false,
            \"aceitatipo\": 1,
            \"tamanho\": 18,
            \"tipoobj\": \"text\"
        }'");

        DB::statement("COMMENT ON COLUMN escola.transposicao.ed353_ativa IS '{
            \"descricao\": \"Armazena se a transposiчуo estс ativa\",
            \"rotulo\": \"ed353_ativa\",
            \"rotulorel\": \"ed353_ativa\",
            \"maiusculo\": false,
            \"autocompl\": false,
            \"aceitatipo\": 5,
            \"tamanho\": 18,
            \"tipoobj\": \"text\"
        }'");

        DB::statement("COMMENT ON COLUMN escola.transposicao.ed353_processada IS '{
            \"descricao\": \"Armazena se a transposiчуo foi processada\",
            \"rotulo\": \"ed353_processada\",
            \"rotulorel\": \"ed353_processada\",
            \"maiusculo\": false,
            \"autocompl\": false,
            \"aceitatipo\": 5,
            \"tamanho\": 18,
            \"tipoobj\": \"text\"
        }'");

        DB::select("SELECT fc_gera_dicionario_apartir_tabela(?, ?)", ['escola', 'transposicao']);

        DB::statement("ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl DISABLE;");
        DB::statement("ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop DISABLE;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP TABLE IF EXISTS escola.transposicao CASCADE");
    }
}
