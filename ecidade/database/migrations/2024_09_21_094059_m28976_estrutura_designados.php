<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M28976EstruturaDesignados extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->criaEstrutura();
        $this->adicionaComentario();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('escola.designadostransposicao');
    }

    private function criaEstrutura()
    {
        DB::statement("
            CREATE TABLE IF NOT EXISTS escola.designadostransposicao (
                ed354_codigo BIGSERIAL PRIMARY KEY,
                ed354_transposicao BIGINT NOT NULL,
                ed354_aluno BIGINT NOT NULL,
                ed354_escoladestino BIGINT NOT NULL,
                ed354_etapadestino BIGINT NOT NULL,
                ed354_opcao BIGINT NOT NULL,
                ed354_pontuacao BIGINT NOT NULL,
                ed354_deficiente BIGINT NOT NULL,
                CONSTRAINT escola_designadostransposicao_ed354_aluno_fk FOREIGN KEY (ed354_aluno) REFERENCES escola.aluno(ed47_i_codigo) ON DELETE CASCADE,
                CONSTRAINT escola_designadostransposicao_ed354_escoladestino_fk FOREIGN KEY (ed354_escoladestino) REFERENCES escola.escola(ed18_i_codigo),
                CONSTRAINT escola_designadostransposicao_ed354_etapadestino_fk FOREIGN KEY (ed354_etapadestino) REFERENCES escola.serie(ed11_i_codigo),
                CONSTRAINT escola_designadostransposicao_ed354_transposicao_fk FOREIGN KEY (ed354_transposicao) REFERENCES escola.transposicao(ed353_codigo),
                UNIQUE (ed354_transposicao, ed354_aluno)
            )
        ");
    }

    private function adicionaComentario()
    {
        // Habilitando triggers
        DB::statement("ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;");
        DB::statement("ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;");

        // Adicionando comentários à tabela
        DB::statement("COMMENT ON TABLE escola.designadostransposicao IS '{
            \"descricao\": \"Armazena dos designados.\",
            \"sigla\": \"ed354\",
            \"dataincl\": \"2024-09-21\",
            \"rotulo\": \"designadostransposicao\",
            \"tipotabela\": 0,
            \"naolibclass\": false,
            \"naolibfunc\": false,
            \"naolibprog\": false,
            \"naolibform\": false
        }'");

        // Adicionando comentários às colunas
        DB::statement("COMMENT ON COLUMN escola.designadostransposicao.ed354_codigo IS '{
            \"descricao\": \"Chave primária da tabela\",
            \"rotulo\": \"ed354_codigo\",
            \"rotulorel\": \"ed354_codigo\",
            \"maiusculo\": false,
            \"autocompl\": false,
            \"aceitatipo\": 1,
            \"tamanho\": 18,
            \"tipoobj\": \"text\"
        }'");

        DB::statement("COMMENT ON COLUMN escola.designadostransposicao.ed354_transposicao IS '{
            \"descricao\": \"Código da transposição\",
            \"rotulo\": \"ed354_transposicao\",
            \"rotulorel\": \"ed354_transposicao\",
            \"maiusculo\": false,
            \"autocompl\": false,
            \"aceitatipo\": 1,
            \"tamanho\": 18,
            \"tipoobj\": \"text\"
        }'");

        DB::statement("COMMENT ON COLUMN escola.designadostransposicao.ed354_aluno IS '{
            \"descricao\": \"Código do aluno\",
            \"rotulo\": \"ed354_aluno\",
            \"rotulorel\": \"ed354_aluno\",
            \"maiusculo\": false,
            \"autocompl\": false,
            \"aceitatipo\": 1,
            \"tamanho\": 18,
            \"tipoobj\": \"text\"
        }'");

        DB::statement("COMMENT ON COLUMN escola.designadostransposicao.ed354_escoladestino IS '{
            \"descricao\": \"Código da escola de destino\",
            \"rotulo\": \"ed354_escoladestino\",
            \"rotulorel\": \"ed354_escoladestino\",
            \"maiusculo\": false,
            \"autocompl\": false,
            \"aceitatipo\": 1,
            \"tamanho\": 18,
            \"tipoobj\": \"text\"
        }'");

        DB::statement("COMMENT ON COLUMN escola.designadostransposicao.ed354_etapadestino IS '{
            \"descricao\": \"Código da etapa de destino\",
            \"rotulo\": \"ed354_etapadestino\",
            \"rotulorel\": \"ed354_etapadestino\",
            \"maiusculo\": false,
            \"autocompl\": false,
            \"aceitatipo\": 1,
            \"tamanho\": 18,
            \"tipoobj\": \"text\"
        }'");

        DB::statement("COMMENT ON COLUMN escola.designadostransposicao.ed354_opcao IS '{
            \"descricao\": \"Opção escolhida\",
            \"rotulo\": \"ed354_opcao\",
            \"rotulorel\": \"ed354_opcao\",
            \"maiusculo\": false,
            \"autocompl\": false,
            \"aceitatipo\": 1,
            \"tamanho\": 18,
            \"tipoobj\": \"text\"
        }'");

        DB::statement("COMMENT ON COLUMN escola.designadostransposicao.ed354_pontuacao IS '{
            \"descricao\": \"Pontuação obtida\",
            \"rotulo\": \"ed354_pontuacao\",
            \"rotulorel\": \"ed354_pontuacao\",
            \"maiusculo\": false,
            \"autocompl\": false,
            \"aceitatipo\": 1,
            \"tamanho\": 18,
            \"tipoobj\": \"text\"
        }'");

        DB::statement("COMMENT ON COLUMN escola.designadostransposicao.ed354_deficiente IS '{
            \"descricao\": \"Código da deficiência\",
            \"rotulo\": \"ed354_deficiente\",
            \"rotulorel\": \"ed354_deficiente\",
            \"maiusculo\": false,
            \"autocompl\": false,
            \"aceitatipo\": 1,
            \"tamanho\": 18,
            \"tipoobj\": \"text\"
        }'");

        DB::select("SELECT fc_gera_dicionario_apartir_tabela(?, ?)", ['escola', 'designadostransposicao']);

        DB::statement("ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl DISABLE;");
        DB::statement("ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop DISABLE;");
    }
}
