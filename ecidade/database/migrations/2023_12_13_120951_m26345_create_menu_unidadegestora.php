<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class M26345CreateMenuUnidadegestora extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // configuracoes
        DB::statement("insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 229005 ,'Configurações' ,'Configurações TCE-RJ' ,'' ,'1' ,'1' ,'Configurações TCE-RJ' ,'true' )");
        DB::statement("insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 8997 ,229005 ,11 ,209 )");

        // unidade gestora
        DB::statement("insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 229006 ,'Unidade Gestora' ,'Unidade Gestora' ,'web/financeiro/contabilidade/tce/rj/sigfis/unidadegestora' ,'1' ,'1' ,'Unidade Gestora - TCE - RJ SIGFIS' ,'true' )");
        DB::statement("insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 229005 ,229006 ,1 ,209 )");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("delete from db_menu where id_item_filho in (229005, 229006) AND modulo = 209");
        DB::statement("delete from db_itensmenu where id_item in (229005, 229006)");
    }
}
