<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M26381AdicionaCampoLabparametros extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('laboratorio.lab_parametros', function (Blueprint $table) {
            $table->tinyInteger('la49_modelolaudo')->default(1);
        });

        DB::connection()->getPdo()->exec(<<<SQL

          ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;
          ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;

           COMMENT ON COLUMN laboratorio.lab_parametros.la49_modelolaudo IS
              '{
                  "descricao": "Permite escolher um valor padrão para o modelo de laudo de exame a ser imprimido.",
                  "rotulo": "Modelo de Laudo",
                  "rotulorel": "Modelo de Laudo",
                  "maiusculo": false,
                  "autocompl": false,
                  "aceitatipo": 0,
                  "tamanho": 10,
                  "tipoobj": "select"
               }';

           SELECT fc_gera_dicionario_apartir_tabela('laboratorio', 'lab_parametros');

          SELECT fc_atualiza_dicionario_apartir_comentario('table column',
        'laboratorio.lab_parametros.la49_modelolaudo',
        '{ "descricao": "Permite escolher um valor padrão para o modelo de laudo de exame a ser imprimido.",
                      "rotulo": "Modelo de Laudo",
                      "rotulorel": "Modelo de Laudo",
                      "maiusculo": false,
                      "autocompl": false,
                      "aceitatipo": 0,
                      "tamanho": 10,
                      "tipoobj": "select"
                    }') ;

        select configuracoes.fc_auditoria_cria_funcao('laboratorio.lab_parametros');

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
        DB::connection()->getPdo()->exec(<<<SQL

            SELECT configuracoes.fc_auditoria_remove_funcao('laboratorio.lab_parametros');
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;

SQL
        );

        Schema::table('laboratorio.lab_parametros', function (Blueprint $table) {
            $table->dropColumn('la49_modelolaudo');
        });

        DB::statement('ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl DISABLE;');
        DB::statement('ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop DISABLE;');
    }
}
