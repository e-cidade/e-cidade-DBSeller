<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M25741AddColunaP78MensageriaTableProcandamint extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->upColunaMensageria();
        $this->upDicionarioDeDados();
    }

    public function upColunaMensageria()
    {
        Schema::table('protocolo.procandamint', function (Blueprint $table) {
            $table->bigInteger('p78_mensageria')
                ->nullable()
                ->after('p78_tipodespacho');
            $table->foreign('p78_mensageria')->references('p123_id')->on('protocolo.mensageria');
        });
    }

    public function upDicionarioDeDados()
    {
        DB::connection()->getPdo()->exec(<<<SQL
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;

            COMMENT ON COLUMN protocolo.procandamint.p78_mensageria IS
              '{
                  "descricao": "Id da mensagem que originou o despacho",
                  "rotulo": "p78_mensageria",
                  "rotulorel": "p78_mensageria",
                  "maiusculo": false,
                  "autocompl": false,
                  "aceitatipo": 1,
                  "tamanho": 10,
                  "tipoobj": "text"
               }';

            SELECT fc_gera_dicionario_apartir_tabela('protocolo', 'procandamint');

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
        $this->downColunaMensageria();
    }

    public function downColunaMensageria()
    {
        Schema::table('protocolo.procandamint', function (Blueprint $table) {
            $table->dropForeign(['p78_mensageria']);
            $table->dropColumn('p78_mensageria');
        });
    }
}
