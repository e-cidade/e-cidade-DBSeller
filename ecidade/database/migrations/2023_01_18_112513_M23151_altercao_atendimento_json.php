<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M23151AltercaoAtendimentoJson extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->upMenu();
    }

    public function upMenu()
    {
        DB::connection()->getPdo()->exec(<<<SQL
        insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 228833 ,'Alterar Json de Atendimento ' ,'Tela para alteração do arquivo de json' ,'web/patrimonial/protocolo/atendimento-ajustar-json ' ,'1' ,'1' ,'Tela para alteração do arquivo de json utilizado para solicitação de atendimento.' ,'true' );
        insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 32 ,228833 ,567 ,604 );
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
        $this->downMenu();
    }

    public function downMenu()
    {
        DB::connection()->getPdo()->exec(<<<SQL
        DELETE FROM  db_menu WHERE id_item_filho = 228833;
        DELETE FROM db_itensmenu WHERE id_item = 228833;
SQL
        );
    }

}
