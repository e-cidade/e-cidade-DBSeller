<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class M25056CriarMenuSalaoParceiro extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(<<<SQL
           insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ,api ) values ( 229312 ,'Salуo Parceiro' ,'Salуo Parceiro' ,'' ,'1' ,'1' ,'Salуo Parceiro' ,'true' ,'false' );
           insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 29 ,229312 ,318 ,40 );
           insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ,api ) values ( 229313 ,'Inclusуo' ,'Inclusуo de Salуo Parceiro' ,'web/tributario/issqn/cadastros/salao-parceiro/inclusao-salao-parceiro' ,'1' ,'1' ,'Inclusуo de Salуo Parceiro' ,'true' ,'false' );
           insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 229312 ,229313 ,1 ,40 );
           insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ,api ) values ( 229314 ,'Alteraчуo' ,'Alteraчуo de Salуo Parceiro' ,'web/tributario/issqn/cadastros/salao-parceiro/alterar-salao-parceiro' ,'1' ,'1' ,'Alteraчуo de Salуo Parceiro' ,'true' ,'false' );
           insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 229312 ,229314 ,2 ,40 );
           insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ,api ) values ( 229315 ,'Inclusуo de Deduчуo' ,'Inclusуo de Deduчуo da Atividade' ,'web/tributario/issqn/cadastros/salao-parceiro/inclusao-deducao-salao' ,'1' ,'1' ,'Inclusуo de Deduчуo da Atividade' ,'true' ,'false' );
           insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 229312 ,229315 ,3 ,40 );

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
            delete from db_menu where id_item_filho in (229312,229313,229314, 229315) and modulo = 40;
            delete from db_itensmenu where id_item in (229312,229313,229314, 229315);
SQL
        );
    }
}
