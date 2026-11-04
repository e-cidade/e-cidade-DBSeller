<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M28584Itemmenu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(<<<SQL
        insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 229279 ,'Manutenчуo de falhas de jobs' ,'Manutenчуo de falhas de jobs' ,'web/configuracao/jobs/failed' ,'1' ,'1' ,'Consulta de jobs falhado, reprocessamento e exclusуo' ,'true' );
        insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 32 ,229279 ,585 ,1 );
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
        DELETE FROM  db_menu WHERE  id_item_filho = 229279;
        DELETE FROM db_itensmenu WHERE id_item = 229279;
SQL
        );
    }
}
