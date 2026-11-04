<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M26381AdicionaCampoLa49exibeliberacaoporexameLabparametros extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('laboratorio.lab_parametros', function (Blueprint $table) {
            $table->boolean('la49_exibeliberacaoporexame')->default(true);
        });

        DB::connection()->getPdo()->exec(<<<SQL

          ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;
          ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;

           COMMENT ON COLUMN laboratorio.lab_parametros.la49_exibeliberacaoporexame IS
              '{
                  "descricao": "Permite escolher a seleção do respectivo campo na rotina de laudo do exame.",
                  "rotulo": "Exibir Liberação por Exame",
                  "rotulorel": "Exibir Liberação por Exame",
                  "maiusculo": false,
                  "autocompl": false,
                  "aceitatipo": 5,
                  "tamanho": 10,
                  "tipoobj": "text"
               }';

           SELECT fc_gera_dicionario_apartir_tabela('laboratorio', 'lab_parametros');

          SELECT fc_atualiza_dicionario_apartir_comentario('table column',
        'laboratorio.lab_parametros.la49_exibeliberacaoporexame',
        '{ "descricao": "Permite escolher a seleção do respectivo campo na rotina de laudo do exame.",
                      "rotulo": "Exibir Liberação por Exame",
                      "rotulorel": "Exibir Liberação por Exame",
                      "maiusculo": false,
                      "autocompl": false,
                      "aceitatipo": 5,
                      "tamanho": 10,
                      "tipoobj": "text"
                    }') ;

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
        DB::statement('ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;');
        DB::statement('ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;');

        Schema::table('laboratorio.lab_parametros', function (Blueprint $table) {
            $table->dropColumn('la49_exibeliberacaoporexame');
        });

        DB::statement('ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl DISABLE;');
        DB::statement('ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop DISABLE;');
    }
}
