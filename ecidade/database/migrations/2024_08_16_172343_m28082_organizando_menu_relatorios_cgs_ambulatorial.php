<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class M28082OrganizandoMenuRelatoriosCgsAmbulatorial extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
DB::unprepared(<<<SQL

update db_itensmenu set descricao = 'Controle CGSs Múltiplos' , help = 'Controle CGSs Múltiplos',desctec = 'Controle CGSs Múltiplos' where id_item = 8074;
insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 229297 ,'CGS' ,'CGS' ,'' ,'1' ,'1' ,'CGS' ,'true' );
insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 30 ,229297 ,863 ,1000004 );

delete from db_menu where id_item_filho = 8075 AND modulo = 1000004;
delete from db_menu where id_item_filho = 8074 AND modulo = 1000004;
delete from db_menu where id_item_filho = 8464 AND modulo = 1000004;

insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 229297 ,8075 ,1 ,1000004 );
insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 229297 ,8074 ,2 ,1000004 );
insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 229297 ,8464 ,3 ,1000004 );
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

update db_itensmenu set descricao = 'CGS Duplos',help = 'CGS Duplos',desctec = 'CGS Duplos' where id_item = 8074;

delete from db_menu where id_item_filho = 8074 AND modulo = 1000004;
delete from db_menu where id_item_filho = 8075 AND modulo = 1000004;
delete from db_menu where id_item_filho = 8464 AND modulo = 1000004;

insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 30 ,8074 ,360,1000004 );
insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 30 ,8075 ,361 ,1000004 );
insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 30 ,8464 ,374 ,1000004 );

delete from db_menu where id_item_filho = 229297 AND modulo = 1000004;
delete from db_itensmenu where id_item = 229297;
SQL
        );
    }
}
