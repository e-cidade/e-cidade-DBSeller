<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M29399ArrumaComentarioLabparametros extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(<<<SQL

      ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;
      ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;

        COMMENT ON COLUMN laboratorio.lab_parametros.la49_identificaprioridade IS
  '{
      "descricao": "Indica se o tipo de pioridade deve ser exibida na etiqueta.",
      "rotulo": "Habilitar Identificaчуo Prioridade de Amostra.",
      "rotulorel": "Exibir Prioridade.",
      "maiusculo": false,
      "autocompl": false,
      "aceitatipo": 5,
      "tamanho": 10,
      "tipoobj": "text"
   }';

      SELECT fc_gera_dicionario_apartir_tabela('laboratorio', 'lab_parametros');

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
        return;
    }
}
