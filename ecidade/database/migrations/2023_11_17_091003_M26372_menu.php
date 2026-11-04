<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M26372Menu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 228995 ,'Lançamento Manual' ,'Lançamento Manual' ,'web/financeiro/contabilidade/procedimento/lancamento/manual' ,'1' ,'1' ,'Nova rotina de lançamento manual' ,'true' );
insert into db_menu(id_item, id_item_filho, menusequencia, modulo) values (3386 ,228995 ,6 ,209 );
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
delete from db_menu where id_item_filho = 228995 AND modulo = 209;
delete from db_itensmenu where id_item = 228995;
SQL
        );
    }
}
