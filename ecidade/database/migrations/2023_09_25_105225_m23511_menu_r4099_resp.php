<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M23511MenuR4099Resp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<SQL
            insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 2001188 ,'Responsável R-4099' ,'Responsável R-4099' , 'web/integracoes/efd-reinf/retencao/dadosrespR4099' ,'1' ,'1' ,'Dados do responsavel do evento R-4099' ,'true' );
            insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 228079 ,2001188 ,7 ,228077 );
SQL;
        DB::connection()->getPdo()->exec($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $sql = <<<SQL
            delete from db_menu where id_item_filho = 2001188 AND modulo = 228077;
            delete from db_itensmenu where id_item = 2001188;
SQL;
        DB::connection()->getPdo()->exec($sql);
    }
}
