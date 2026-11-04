<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class M25926MenuExtratoAnuParcel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
        insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 229004 ,'Extrato Anulação de Parcelamento' ,'Extrato Anulação de Parcelamento' ,'arr3_extratoanulacaoparcelamento001.php' ,'1' ,'1' ,'Extrato Anulação de Parcelamento' ,'true' );
        delete from db_menu where id_item_filho = 229004 AND modulo = 1985522;
        insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 32 ,229004 ,581 ,1985522 );
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
        delete from db_menu where id_item_filho = 229004 AND modulo = 1985522;
        delete from db_itensmenu where  id_item = 229004;
SQL
    );
    }
}
