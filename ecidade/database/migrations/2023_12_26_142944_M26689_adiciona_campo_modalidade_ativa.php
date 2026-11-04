<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M26689AdicionaCampoModalidadeAtiva extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('licitacao.cflicita', function (Blueprint $table) {
            $table->boolean('l03_modalidadeativa')->default(true);
        });

        DB::connection()->getPdo()->exec(<<<SQL

          ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;
          ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;

           COMMENT ON COLUMN licitacao.cflicita.l03_modalidadeativa IS
              '{
                  "descricao": "Define se a modalidade está ativa ou não",
                  "rotulo": "Modalidade Ativa",
                  "rotulorel": "Modalidade Ativa",
                  "maiusculo": false,
                  "autocompl": false,
                  "aceitatipo": 5,
                  "tamanho": 10,
                  "tipoobj": "text"
               }';

           SELECT fc_gera_dicionario_apartir_tabela('licitacao', 'cflicita');

          SELECT fc_atualiza_dicionario_apartir_comentario('table column',
        'licitacao.cflicita.l03_modalidadeativa',
        '{ "descricao": "Define se a modalidade está ativa ou não",
                  "rotulo": "Modalidade Ativa",
                  "rotulorel": "Modalidade Ativa",
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

        Schema::table('licitacao.cflicita', function (Blueprint $table) {
            $table->dropColumn('l03_modalidadeativa');
        });

        DB::statement('ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl DISABLE;');
        DB::statement('ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop DISABLE;');
    }
}
