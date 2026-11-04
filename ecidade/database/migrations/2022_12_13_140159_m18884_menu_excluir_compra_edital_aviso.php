<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M18884MenuExcluirCompraEditalAviso extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 228822 ,'Exclusão' ,'Exclusão' ,'pncp1_exclusaocompraeditalaviso003.php' ,'1' ,'1' ,'Exclusão da Compra/Edital/Aviso' ,'true' );
insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 228816 ,228822 ,3 ,228802 );
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
delete from db_itensmenu where id_item = 228822;
delete from db_menu where id_item_filho = 228822 AND modulo = 228802;
SQL
        );
    }
}
