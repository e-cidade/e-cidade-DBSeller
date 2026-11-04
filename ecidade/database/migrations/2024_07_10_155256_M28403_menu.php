<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M28403Menu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
	DB::unprepared(<<<SQL
insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 229273 ,'Acompanhamento do PPA/LDO' ,'Acompanhamento do PPA/LDO' ,'pla2_acompanhamento.php' ,'1' ,'1' ,'Acompanhamento do PPA/LDO' ,'true' );
insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 228497 ,229273 ,13 ,228358 );
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
delete from db_menu where id_item_filho = 229273 AND modulo = 228358;
delete from db_itensmenu where id_item = 229273;
SQL
        );
    }
}
