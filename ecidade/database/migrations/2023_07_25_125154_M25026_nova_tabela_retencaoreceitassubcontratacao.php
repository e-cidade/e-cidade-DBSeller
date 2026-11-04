<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M25026NovaTabelaRetencaoreceitassubcontratacao extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->criaTabela();
        $this->upDicionario();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("select fc_remove_dicionario_tabela('empenho', 'retencaoreceitassubcontratacao');");
        Schema::drop('empenho.retencaoreceitassubcontratacao');
    }

    public function criaTabela()
    {
        Schema::create("empenho.retencaoreceitassubcontratacao", function (Blueprint $table) {
            $table->bigIncrements("e163_sequencial");
            $table->integer("e163_retencaoreceitas");
            $table->decimal("e163_valor", 8, 2);
            $table->integer("e163_retencaotiporec");
            $table->integer("e163_numcgm");
            $table->foreign('e163_retencaoreceitas')->references('e23_sequencial')->on('retencaoreceitas');
            $table->foreign('e163_retencaotiporec')->references('e21_sequencial')->on('retencaotiporec');
            $table->foreign('e163_numcgm')->references('z01_numcgm')->on('cgm');
        });

        DB::statement("SELECT configuracoes.fc_auditoria_cria_funcao('empenho.retencaoreceitassubcontratacao');");
    }

    public function upDicionario()
    {
        DB::connection()->getPdo()->exec(<<<SQL


        COMMENT ON TABLE empenho.retencaoreceitassubcontratacao IS
          '{
              "descricao": "Tabela responsavel pelas subcontratacoes da retencao",
              "sigla": "e163",
              "dataincl": "2023-07-25",
              "rotulo": "Subcontratações da Retenção",
              "tipotabela": 0,
              "naolibclass": false,
              "naolibfunc": false,
              "naolibprog": false,
              "naolibform": false
           }';

        COMMENT ON COLUMN empenho.retencaoreceitassubcontratacao.e163_sequencial IS
          '{
              "descricao": "Chave primária da tabela",
              "rotulo": "Código",
              "rotulorel": "Código",
              "maiusculo": false,
              "autocompl": false,
              "aceitatipo": 1,
              "tamanho": 10,
              "tipoobj": "text"
           }';

        COMMENT ON COLUMN empenho.retencaoreceitassubcontratacao.e163_retencaoreceitas IS
          '{
              "descricao": "Chave estrangeira referenciando a PK da tabela retencaoreceitas",
              "rotulo": "Código retencaoreceitas",
              "rotulorel": "Código retencaoreceitas",
              "maiusculo": false,
              "autocompl": false,
              "aceitatipo": 1,
              "tamanho": 10,
              "tipoobj": "text"
           }';

        COMMENT ON COLUMN empenho.retencaoreceitassubcontratacao.e163_valor IS
          '{
              "descricao": "Campo valor da subcontratação",
              "rotulo": "Valor subcontratação",
              "rotulorel": "Valor subcontratação",
              "maiusculo": false,
              "autocompl": false,
              "aceitatipo": 4,
              "tamanho": 10,
              "tipoobj": "text"
           }';

        COMMENT ON COLUMN empenho.retencaoreceitassubcontratacao.e163_retencaotiporec IS
          '{
              "descricao": "Chave estrangeira referenciando a PK da tabela retencaotiporec",
              "rotulo": "Código retencaotiporec",
              "rotulorel": "Código retencaotiporec",
              "maiusculo": false,
              "autocompl": false,
              "aceitatipo": 1,
              "tamanho": 10,
              "tipoobj": "text"
           }';

        COMMENT ON COLUMN empenho.retencaoreceitassubcontratacao.e163_numcgm IS
          '{
              "descricao": "Chave estrangeira referenciando a PK da tabela cgm",
              "rotulo": "Código cgm",
              "rotulorel": "Código cgm",
              "maiusculo": false,
              "autocompl": false,
              "aceitatipo": 1,
              "tamanho": 10,
              "tipoobj": "text"
           }';

        SELECT fc_gera_dicionario_apartir_tabela('empenho', 'retencaoreceitassubcontratacao');

SQL
        );
    }
}
