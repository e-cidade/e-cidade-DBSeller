<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M25216MenuCancelamentoPacelLista extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
            insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 228957 ,'Anulação de Parcelamento por Lista' ,'Anulação de Parcelamento por Lista' ,'web/tributario/arrecadacao/procedimentos/cancelparcelista' ,'1' ,'1' ,'Cancela parcelamento através de lista.' ,'true' );
            delete from db_menu where id_item_filho = 228957 AND modulo = 1985522;
            insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 32 ,228957 ,578 ,1985522 );
            delete from db_menu where id_item_filho = 228957 AND modulo = 1985522;
            insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 32 ,228957 ,578 ,1985522 );
        
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
        //
    }
}