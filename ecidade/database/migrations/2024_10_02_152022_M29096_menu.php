<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M29096Menu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(<<<SQL
insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ,api )
values ( 229316 ,'Balancete Receita por Complemento' ,'Balancete Receita por Complemento' ,'web/financeiro/contabilidade/relatorios/balancetes/receita/recurso' ,'1' ,'1' ,'Balancete Receita por Complemento' ,'true' ,'false' );

insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo )
values ( 4065 ,229316 ,19 ,209 );

update db_itensmenu set libcliente = false where id_item = 228553;
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
delete from db_menu where id_item_filho = 229316 AND modulo = 209;
delete from db_itensmenu where id_item = 229316;
update db_itensmenu set libcliente = true where id_item = 228553;
SQL
        );
    }
}
