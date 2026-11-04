<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M29579Menu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(<<<SQL
insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ,api ) values ( 229365 ,'Superávit Financeiro por Recurso' ,'Superávit Financeiro por Recurso' ,'web/financeiro/contabilidade/relatorios/conferencia/superavit-por-recurso' ,'1' ,'1' ,'Superávit Financeiro por Recurso' ,'true' ,'false' );
insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 4189 ,229365 ,17 ,209 );
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
delete from db_menu where id_item_filho = 229365 AND modulo = 209;
delete from db_itensmenu where id_item = 229365;
SQL
        );
    }
}
