<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M29008CreateLabrequisicaoPrioridadetipoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('laboratorio.labrequisicao_prioridadetipo', function (Blueprint $table) {
            $table->bigIncrements('la70_sequencial');
            $table->unsignedBigInteger('la70_labrequisicao');
            $table->integer('la70_prioridadetipo');

            $table->foreign('la70_labrequisicao')->references('la22_i_codigo')->on('laboratorio.lab_requisicao');
            $table->foreign('la70_prioridadetipo')->references('la69_sequencial')->on('laboratorio.prioridadetipo');
        });

        DB::unprepared(<<<SQL

      ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;
      ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;

COMMENT ON TABLE laboratorio.labrequisicao_prioridadetipo IS '{
  "descricao": "Tabela de vínculo entre as tabelas lab_requisicao e prioridadetipo.",
  "sigla": "la70",
  "dataincl": "2024-10-18",
  "rotulo": "Requisições com prioridade",
  "tipotabela": 0,
  "naolibclass": false,
  "naolibfunc": false,
  "naolibprog": false,
  "naolibform": false
}';

        SELECT fc_gera_dicionario_apartir_tabela('laboratorio', 'labrequisicao_prioridadetipo');

COMMENT ON COLUMN laboratorio.labrequisicao_prioridadetipo.la70_labrequisicao IS '{
  "descricao": "Código identificador da tabela lab_requisicao.",
  "rotulo": "Campo identificador lab_requisicao.",
  "rotulorel": "Campo identificador lab_requisicao.",
  "maiusculo": false,
  "autocompl": true,
  "aceitatipo": 1,
  "tamanho": 10,
  "tipoobj": "text"
}';

        SELECT fc_gera_dicionario_apartir_tabela('laboratorio', 'labrequisicao_prioridadetipo');

COMMENT ON COLUMN laboratorio.labrequisicao_prioridadetipo.la70_prioridadetipo IS '{
  "descricao": "Código identificador da tabela prioridadetipo.",
  "rotulo": "Campo identificador prioridadetipo.",
  "rotulorel": "Campo identificador prioridadetipo.",
  "maiusculo": false,
  "autocompl": true,
  "aceitatipo": 1,
  "tamanho": 10,
  "tipoobj": "text"
}';

      SELECT fc_gera_dicionario_apartir_tabela('laboratorio', 'labrequisicao_prioridadetipo');

      SELECT configuracoes.fc_auditoria_cria_funcao('laboratorio.labrequisicao_prioridadetipo');

ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl DISABLE;
ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop DISABLE;

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
        Schema::dropIfExists('laboratorio.labrequisicao_prioridadetipo');
    }
}
