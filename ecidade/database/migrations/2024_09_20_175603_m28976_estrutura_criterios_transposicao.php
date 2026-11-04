<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M28976EstruturaCriteriosTransposicao extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->criaCriteriosTransposicao();
        $this->criaCriteriosTransposicaoOrdenacao();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->dropaCriteriosTransposicaoOrdenacao();
        $this->dropaCriteriosTransposicao();
    }

    private function criaCriteriosTransposicao()
    {
        DB::statement('
            CREATE TABLE IF NOT EXISTS escola.criteriostransposicao (
                ed351_codigo BIGSERIAL PRIMARY KEY,
                ed351_descricao VARCHAR(255)
            )
        ');

        // Habilitando triggers
        DB::statement("ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;");
        DB::statement("ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;");

        // Adicionando comentários à tabela
        DB::statement("COMMENT ON TABLE escola.criteriostransposicao IS '{
            \"descricao\": \"Armazena as informações dos critérios de transposição.\",
            \"sigla\": \"ed351\",
            \"dataincl\": \"2024-09-20\",
            \"rotulo\": \"criteriostransposicao\",
            \"tipotabela\": 0,
            \"naolibclass\": false,
            \"naolibfunc\": false,
            \"naolibprog\": false,
            \"naolibform\": false
        }'");

        // Adicionando comentários às colunas
        DB::statement("COMMENT ON COLUMN escola.criteriostransposicao.ed351_codigo IS '{
            \"descricao\": \"Chave primária da tabela\",
            \"rotulo\": \"ed351_codigo\",
            \"rotulorel\": \"ed351_codigo\",
            \"maiusculo\": false,
            \"autocompl\": false,
            \"aceitatipo\": 1,
            \"tamanho\": 18,
            \"tipoobj\": \"text\"
        }'");

        DB::statement("COMMENT ON COLUMN escola.criteriostransposicao.ed351_descricao IS '{
            \"descricao\": \"Descrição do critério de transposição\",
            \"rotulo\": \"ed351_descricao\",
            \"rotulorel\": \"ed351_descricao\",
            \"maiusculo\": false,
            \"autocompl\": false,
            \"aceitatipo\": 1,
            \"tamanho\": 18,
            \"tipoobj\": \"text\"
        }'");

        DB::select("SELECT fc_gera_dicionario_apartir_tabela(?, ?)", ['escola', 'vagastransposicao']);

        DB::statement("ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl DISABLE;");
        DB::statement("ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop DISABLE;");

        DB::table('escola.criteriostransposicao')->insert([
            ['ed351_descricao' => 'Deficiência'],
            ['ed351_descricao' => 'Bolsa Família'],
            ['ed351_descricao' => 'Irmão na Instituição'],
            ['ed351_descricao' => 'Idade Maior'],
            ['ed351_descricao' => 'Servidor na Escola']
        ]);
    }

    private function dropaCriteriosTransposicao()
    {
        DB::statement('DROP TABLE IF EXISTS escola.criteriostransposicao CASCADE');
    }

    private function criaCriteriosTransposicaoOrdenacao()
    {
        DB::statement("
            CREATE TABLE IF NOT EXISTS escola.criteriostransposicaoordenacao(
                ed352_codigo BIGSERIAL PRIMARY KEY,
                ed352_ano INTEGER NOT NULL,
                ed352_etapa INTEGER NOT NULL,
                ed352_criterio INTEGER NOT NULL,
                ed352_peso INTEGER NOT NULL,
                ed352_ordem INTEGER NOT NULL,
                CONSTRAINT escola_criteriostransposicaoordenacao_ed352_criterio_fk FOREIGN KEY (ed352_criterio) REFERENCES escola.criteriostransposicao (ed351_codigo) ON DELETE CASCADE,
                CONSTRAINT escola_criteriostransposicaoordenacao_ed352_etapa_fk FOREIGN KEY (ed352_etapa) REFERENCES escola.serie (ed11_i_codigo)
            )
        ");

        // Habilitando triggers
        DB::statement("ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;");
        DB::statement("ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;");

        // Adicionando comentários à tabela
        DB::statement("COMMENT ON TABLE escola.criteriostransposicaoordenacao IS '{
            \"descricao\": \"Armazena as informações das vagas de transposição.\",
            \"sigla\": \"ed352\",
            \"dataincl\": \"2024-09-20\",
            \"rotulo\": \"criteriostransposicaoordenacao\",
            \"tipotabela\": 0,
            \"naolibclass\": false,
            \"naolibfunc\": false,
            \"naolibprog\": false,
            \"naolibform\": false
        }'");

        // Adicionando comentários às colunas
        DB::statement("COMMENT ON COLUMN escola.criteriostransposicaoordenacao.ed352_codigo IS '{
            \"descricao\": \"Chave primária da tabela\",
            \"rotulo\": \"ed352_codigo\",
            \"rotulorel\": \"ed352_codigo\",
            \"maiusculo\": false,
            \"autocompl\": false,
            \"aceitatipo\": 1,
            \"tamanho\": 18,
            \"tipoobj\": \"text\"
        }'");

        DB::statement("COMMENT ON COLUMN escola.criteriostransposicaoordenacao.ed352_ano IS '{
            \"descricao\": \"Armazena o ano do critério de ordenação\",
            \"rotulo\": \"ed352_ano\",
            \"rotulorel\": \"ed352_ano\",
            \"maiusculo\": false,
            \"autocompl\": false,
            \"aceitatipo\": 1,
            \"tamanho\": 18,
            \"tipoobj\": \"text\"
        }'");

        DB::statement("COMMENT ON COLUMN escola.criteriostransposicaoordenacao.ed352_etapa IS '{
            \"descricao\": \"Armazena a etapa do critério de ordenação\",
            \"rotulo\": \"ed352_etapa\",
            \"rotulorel\": \"ed352_etapa\",
            \"maiusculo\": false,
            \"autocompl\": false,
            \"aceitatipo\": 1,
            \"tamanho\": 18,
            \"tipoobj\": \"text\"
        }'");

        DB::statement("COMMENT ON COLUMN escola.criteriostransposicaoordenacao.ed352_criterio IS '{
            \"descricao\": \"Armazena o critério de ordenação\",
            \"rotulo\": \"ed352_criterio\",
            \"rotulorel\": \"ed352_criterio\",
            \"maiusculo\": false,
            \"autocompl\": false,
            \"aceitatipo\": 1,
            \"tamanho\": 18,
            \"tipoobj\": \"text\"
        }'");

        DB::statement("COMMENT ON COLUMN escola.criteriostransposicaoordenacao.ed352_peso IS '{
            \"descricao\": \"Armazena o peso do critério de ordenação\",
            \"rotulo\": \"ed352_peso\",
            \"rotulorel\": \"ed352_peso\",
            \"maiusculo\": false,
            \"autocompl\": false,
            \"aceitatipo\": 1,
            \"tamanho\": 18,
            \"tipoobj\": \"text\"
        }'");

        DB::statement("COMMENT ON COLUMN escola.criteriostransposicaoordenacao.ed352_ordem IS '{
            \"descricao\": \"Armazena a ordem do critério de ordenação\",
            \"rotulo\": \"ed352_ordem\",
            \"rotulorel\": \"ed352_ordem\",
            \"maiusculo\": false,
            \"autocompl\": false,
            \"aceitatipo\": 1,
            \"tamanho\": 18,
            \"tipoobj\": \"text\"
        }'");

        DB::statement("ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl DISABLE;");
        DB::statement("ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop DISABLE;");
    }

    private function dropaCriteriosTransposicaoOrdenacao()
    {
        DB::statement('DROP TABLE IF EXISTS escola.criteriostransposicaoordenacao');
    }
}
