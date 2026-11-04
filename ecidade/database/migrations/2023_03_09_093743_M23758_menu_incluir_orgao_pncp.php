<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M23758MenuIncluirOrgaoPncp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 228867 ,'Órgão' ,'Órgão' ,'web/patrimonial/pncp/cadastros/orgao ' ,'1' ,'1' ,'Órgão' ,'true' );
insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 228800 ,228867 ,2 ,228802 );

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
        delete from db_menu where id_item_filho = 228867 AND modulo = 228802;
        delete from db_itensmenu where id_item = 228867;
SQL
        );
    }
}
