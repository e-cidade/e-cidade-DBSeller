<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CriacaoMenuFolhaDePonto extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente )
values ( 228972 ,'Folha de Ponto' ,'Folha de Ponto ' ,'rec1_folhaponto01.php' ,'1' ,'1' ,'Folha de Ponto dos Funcionários' ,'true' );
insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 30 ,228972 ,852 ,2323 );
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
delete from db_menu where id_item_filho = 228972 AND modulo = 2323;
delete from db_itensmenu where id_item = 228972;
SQL
        );
    }
}
