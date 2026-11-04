<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M25026RetalorioSubcontratacaoMenu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
        insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 228967 ,'Retenção Subcontratação' ,'Retenção Subcontratação' ,'emp2_relatoriosubcontratacoes001.php' ,'1' ,'1' ,'Relatório de subcontratação das retenções.' ,'true' );
        insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 5603 ,228967 ,19 ,398 );

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
        delete from db_itensmenu where id_item = 228967;
        delete from db_menu where id_item_filho = 228967 AND modulo = 398;
SQL
        );


    }
}
