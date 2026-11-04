<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M24719ControleProtocolos extends Migration
{
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

        Schema::create('farmacia.bnafarbatch_protocolo', function (Blueprint $table) {
            $table->bigIncrements('fa76_id')->comment($this->getDicionarioCampo('fa76_id'));
            $table->bigInteger('fa76_bnafarbatch')->comment($this->getDicionarioCampo('fa76_bnafarbatch'));
            $table->integer('fa76_protocolo')->comment($this->getDicionarioCampo('fa76_protocolo'));
            $table->boolean('fa76_falhou')->default(false)->comment($this->getDicionarioCampo('fa76_falhou'));
            $table->dateTime('fa76_created_at')->comment($this->getDicionarioCampo('fa76_created_at'));

            $table->foreign('fa76_bnafarbatch')->references('fa75_id')->on('farmacia.bnafarbatch');
        });


        DB::statement("COMMENT ON TABLE farmacia.bnafarbatch_protocolo IS '{$this->getDicionarioTabela()}'");
        DB::statement("SELECT fc_gera_dicionario_apartir_tabela('farmacia', 'bnafarbatch_protocolo')");

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
        DB::statement("SELECT fc_remove_dicionario_tabela('farmacia', 'bnafarbatch_protocolo');");

        Schema::dropIfExists('farmacia.bnafarbatch_protocolo');
    }

    private function buildComments()
    {
        $this->comments = [
            'tabela' => [
                "descricao" => mb_convert_encoding("Tabela de ligação do protocolo gerado com o batch em que foi iniciado.", 'UTF-8'),
                "sigla" => "fa76",
                "dataincl" => "2023-08-10",
                "rotulo" => "Bnafar Batch Protocolo",
                "tipotabela" => 0,
                "naolibclass" => true,
                "naolibfunc" => true,
                "naolibprog" => true,
                "naolibform" => true
            ],
            'campos' => [
                'fa76_id' => [
                    "descricao" => mb_convert_encoding("Chave primária da tabela", 'UTF-8'),
                    "rotulo" => mb_convert_encoding("Código", 'UTF-8'),
                    "rotulorel" => mb_convert_encoding("Código", 'UTF-8'),
                    "maiusculo" => false,
                    "autocompl" => false,
                    "aceitatipo" => 1,
                    "tamanho" => 10,
                    "tipoobj" => "text"
                ],
                'fa76_bnafarbatch' => [
                    "descricao" => "Chave estrangeira da tabela bnafarbatch",
                    "rotulo" => mb_convert_encoding("Código da Bnafar Batch", 'UTF-8'),
                    "rotulorel" => mb_convert_encoding("Código da Bnafar Batch", 'UTF-8'),
                    "maiusculo" => false,
                    "autocompl" => false,
                    "aceitatipo" => 1,
                    "tamanho" => 10,
                    "tipoobj" => "text"
                ],
                'fa76_protocolo' => [
                    "descricao" => mb_convert_encoding("Código de protocolo gerado pela API do BNAFAR", 'UTF-8'),
                    "rotulo" => "Protocolo BNAFAR",
                    "rotulorel" => "Protocolo BNAFAR",
                    "maiusculo" => false,
                    "autocompl" => false,
                    "aceitatipo" => 1,
                    "tamanho" => 10,
                    "tipoobj" => "text"
                ],
                'fa76_falhou' => [
                    "descricao" => mb_convert_encoding("Indica se o processamento falhou ou não.", 'UTF-8'),
                    "rotulo" => "Ocorreu Falha",
                    "rotulorel" => "Ocorreu Falha",
                    "maiusculo" => false,
                    "autocompl" => false,
                    "aceitatipo" => 1,
                    "tamanho" => 10,
                    "tipoobj" => "text"
                ],
                'fa76_created_at' => [
                    "descricao" => mb_convert_encoding("Data de inclusão do registro", 'UTF-8'),
                    "rotulo" => "Data",
                    "rotulorel" => "Data",
                    "maiusculo" => false,
                    "autocompl" => false,
                    "aceitatipo" => 1,
                    "tamanho" => 10,
                    "tipoobj" => "text"
                ]
            ]
        ];
    }

    private function getDicionarioCampo($campo)
    {
        if ($this->comments === null) {
            $this->buildComments();
        }

        return mb_convert_encoding(json_encode($this->comments['campos'][$campo]), 'ISO-8859-1');
    }

    private function getDicionarioTabela()
    {
        if ($this->comments === null) {
            $this->buildComments();
        }

        return mb_convert_encoding(json_encode($this->comments['tabela']), 'ISO-8859-1');
    }
}
