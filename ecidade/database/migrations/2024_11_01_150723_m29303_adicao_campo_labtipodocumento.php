<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M29303AdicaoCampoLabtipodocumento extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('laboratorio.lab_tipodocumento', function (Blueprint $table) {
            $table->boolean('la33_ativo')->default(true);
        });

        DB::unprepared(<<<SQL

      ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;
      ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;
            
        COMMENT ON COLUMN laboratorio.lab_tipodocumento.la33_ativo IS
  '{
      "descricao": "Indica se o documento pode ser utilizado",
      "rotulo": "Ativo",
      "rotulorel": "Ativo",
      "maiusculo": false,
      "autocompl": false,
      "aceitatipo": 5,
      "tamanho": 10,
      "tipoobj": "checkbox"
   }';            

      SELECT fc_gera_dicionario_apartir_tabela('laboratorio', 'lab_tipodocumento');
      SELECT fc_gera_dicionario_apartir_tabela('laboratorio', 'lab_tipodocumento');

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
        Schema::table('laboratorio.lab_tipodocumento', function (Blueprint $table) {
            $table->dropColumn('la33_ativo');
        });

    }
}
