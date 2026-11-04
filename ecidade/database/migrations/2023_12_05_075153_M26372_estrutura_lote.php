<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M26372EstruturaLote extends Migration
{
    /**
     * @var array|array[]
     */
    private $comments;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;");
        DB::statement("ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;");

        Schema::create('contabilidade.lotelancamentos', function (Blueprint $table) {
            $table->bigIncrements('c160_codigo')->comment($this->getDicionarioCampo('c160_codigo'));
            $table->integer('c160_exercicio')->comment($this->getDicionarioCampo('c160_exercicio'));
            $table->integer('c160_instituicao')->comment($this->getDicionarioCampo('c160_instituicao'));
            $table->string('c160_lote', 20)->comment($this->getDicionarioCampo('c160_lote'));
            $table->timestamps();
            $table->foreign('c160_instituicao')->references('codigo')->on('configuracoes.db_config');
        });

        Schema::create('contabilidade.lotelancamentoconlancam', function (Blueprint $table) {
            $table->bigIncrements('c161_codigo')->comment($this->getDicionarioCampo('c161_codigo'));
            $table->integer('c161_lotelancamento')->comment($this->getDicionarioCampo('c161_lotelancamento'));
            $table->integer('c161_conlancam')->comment($this->getDicionarioCampo('c161_conlancam'));
            $table->foreign('c161_lotelancamento')->references('c160_codigo')->on('contabilidade.lotelancamentos');
            $table->foreign('c161_conlancam')->references('c70_codlan')->on('contabilidade.conlancam');
        });

        DB::statement("COMMENT ON TABLE contabilidade.lotelancamentos IS '{$this->getDicionarioTabela('lotelancamentos')}'");
        DB::statement("COMMENT ON TABLE contabilidade.lotelancamentoconlancam IS '{$this->getDicionarioTabela('lotelancamentoconlancam')}'");

        DB::statement("SELECT fc_gera_dicionario_apartir_tabela('contabilidade', 'lotelancamentos')");
        DB::statement("SELECT fc_gera_dicionario_apartir_tabela('contabilidade', 'lotelancamentoconlancam')");

        // triggers de audotoria
        DB::statement("select configuracoes.fc_auditoria_cria_funcao('contabilidade.lotelancamentos');");
        DB::statement("select configuracoes.fc_auditoria_cria_funcao('contabilidade.lotelancamentoconlancam');");

        DB::statement("ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl DISABLE;");
        DB::statement("ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop DISABLE;");

        DB::statement("ALTER TABLE lancamentoscontabeislog SET SCHEMA contabilidade");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("SELECT fc_remove_dicionario_tabela('contabilidade', 'lotelancamentoconlancam');");
        DB::statement("SELECT fc_remove_dicionario_tabela('contabilidade', 'lotelancamentos');");

        Schema::dropIfExists('contabilidade.lotelancamentoconlancam');
        Schema::dropIfExists('contabilidade.lotelancamentos');
    }


    private function getDicionarioCampo($campo)
    {
        if ($this->comments === null) {
            $this->buildComments();
        }

        return mb_convert_encoding(json_encode($this->comments['campos'][$campo]), 'ISO-8859-1');
    }

    private function getDicionarioTabela($tabela)
    {
        if ($this->comments === null) {
            $this->buildComments();
        }

        return mb_convert_encoding(json_encode($this->comments['tabela'][$tabela]), 'ISO-8859-1');
    }

    private function buildComments()
    {
        $this->comments = [
            'tabela' => [
                'lotelancamentos' => [
                    "descricao" => mb_convert_encoding("Tabela de lote de lançamento manual.", 'UTF-8'),
                    "sigla" => "c160",
                    "dataincl" => "2023-12-05",
                    "rotulo" => mb_convert_encoding("Lote de lançamentos", 'UTF-8'),
                    "tipotabela" => 0,
                    "naolibclass" => true,
                    "naolibfunc" => true,
                    "naolibprog" => true,
                    "naolibform" => true
                ],
                'lotelancamentoconlancam' => [
                    "descricao" => mb_convert_encoding("Lançamentos do lote.", 'UTF-8'),
                    "sigla" => "c161",
                    "dataincl" => "2023-12-05",
                    "rotulo" => mb_convert_encoding("Lançamentos do lote", 'UTF-8'),
                    "tipotabela" => 0,
                    "naolibclass" => true,
                    "naolibfunc" => true,
                    "naolibprog" => true,
                    "naolibform" => true
                ]
            ],

            'campos' => [
                'c160_codigo' => [
                    "descricao" => mb_convert_encoding("Chave primária da tabela", 'UTF-8'),
                    "rotulo" => mb_convert_encoding("Código", 'UTF-8'),
                    "rotulorel" => mb_convert_encoding("Código", 'UTF-8'),
                    "maiusculo" => false,
                    "autocompl" => false,
                    "aceitatipo" => 1,
                    "tamanho" => 10,
                    "tipoobj" => "text"
                ],
                'c160_lote' => [
                    "descricao" => mb_convert_encoding("Lote", 'UTF-8'),
                    "rotulo" => mb_convert_encoding("Lote", 'UTF-8'),
                    "rotulorel" => mb_convert_encoding("Lote", 'UTF-8'),
                    "maiusculo" => false,
                    "autocompl" => false,
                    "aceitatipo" => 1,
                    "tamanho" => 20,
                    "tipoobj" => "text"
                ],
                'c160_exercicio' => [
                    "descricao" => mb_convert_encoding("Exercicio", 'UTF-8'),
                    "rotulo" => mb_convert_encoding("Exercicio", 'UTF-8'),
                    "rotulorel" => mb_convert_encoding("Exercicio", 'UTF-8'),
                    "maiusculo" => false,
                    "autocompl" => false,
                    "aceitatipo" => 1,
                    "tamanho" => 10,
                    "tipoobj" => "text"
                ],
                'c160_instituicao' => [
                    "descricao" => mb_convert_encoding("Instituição", 'UTF-8'),
                    "rotulo" => mb_convert_encoding("Instituição", 'UTF-8'),
                    "rotulorel" => mb_convert_encoding("Instituição", 'UTF-8'),
                    "maiusculo" => false,
                    "autocompl" => false,
                    "aceitatipo" => 1,
                    "tamanho" => 10,
                    "tipoobj" => "text"
                ],
                'c161_codigo' => [
                    "descricao" => mb_convert_encoding("Chave primária da tabela", 'UTF-8'),
                    "rotulo" => mb_convert_encoding("Código", 'UTF-8'),
                    "rotulorel" => mb_convert_encoding("Código", 'UTF-8'),
                    "maiusculo" => false,
                    "autocompl" => false,
                    "aceitatipo" => 1,
                    "tamanho" => 10,
                    "tipoobj" => "text"],
                'c161_lotelancamento' => [
                    "descricao" => mb_convert_encoding("Referência ao lote", 'UTF-8'),
                    "rotulo" => mb_convert_encoding("Referência", 'UTF-8'),
                    "rotulorel" => mb_convert_encoding("Referência", 'UTF-8'),
                    "maiusculo" => false,
                    "autocompl" => false,
                    "aceitatipo" => 1,
                    "tamanho" => 10,
                    "tipoobj" => "text"
                ],
                'c161_conlancam' => [
                    "descricao" => mb_convert_encoding("Referência ao lançamento", 'UTF-8'),
                    "rotulo" => mb_convert_encoding("Referência ao lançamento", 'UTF-8'),
                    "rotulorel" => mb_convert_encoding("Referência ao lançamento", 'UTF-8'),
                    "maiusculo" => false,
                    "autocompl" => false,
                    "aceitatipo" => 1,
                    "tamanho" => 10,
                    "tipoobj" => "text"
                ],
            ],
        ];
    }
}
