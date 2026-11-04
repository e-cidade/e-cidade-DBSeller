<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M28107AdicionaCampoLabparametros extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('laboratorio.lab_parametros', function (Blueprint $table) {
            $table->boolean('la49_integracaoinfinity')->default(false);
        });

        DB::unprepared(<<<SQL

      ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;
      ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;
            
        COMMENT ON COLUMN laboratorio.lab_parametros.la49_integracaoinfinity IS
  '{
      "descricao": "Indica se a integracao com o sistema infinity está ativada",
      "rotulo": "Integracao Infinity",
      "rotulorel": "Integracao Infinity",
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
        DB::unprepared(<<<SQL

      ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;
      ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;

SQL
        );

        Schema::table('laboratorio.lab_parametros', function (Blueprint $table) {
            $table->dropColumn('la49_integracaoinfinity');
        });

        DB::unprepared(<<<SQL

      ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl DISABLE;
      ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop DISABLE;

SQL
        );
    }
}
