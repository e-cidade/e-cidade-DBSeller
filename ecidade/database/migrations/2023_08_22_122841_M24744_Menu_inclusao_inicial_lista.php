<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class M24744MenuInclusaoInicialLista extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
            insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 228962 ,'Inclusão por Lista' ,'Inclusão por lista' ,'jur4_gerainiciallista001.php' ,'1' ,'1' ,' inclusão de iniciais a partir de lista de débitos gerada no módulo Notificações.' ,'true' );
            delete from db_menu where id_item_filho = 228962 AND modulo = 313;
            insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 1789 ,228962 ,8 ,313 );
        
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
            delete from db_menu where id_item_filho = 228962 AND modulo = 313;
            delete from db_itensmenu where id_item = 228962;
SQL
        );
    }
}
