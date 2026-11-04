<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class M19991ItemMenuAjusteHorus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 228669 ,'Ajuste Inconsistências' ,'Ajuste de inconsistências' ,'far4_ajustehorus.php' ,'1' ,'1' ,'Ajuste de inconsistências nas movimentações.' ,'true' );");
        DB::statement("insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 10247 ,228669 ,3 ,6877 );");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("delete from db_menu where id_item_filho = 228669;");
        DB::statement("delete from db_itensmenu where id_item = 228669;");
    }
}
