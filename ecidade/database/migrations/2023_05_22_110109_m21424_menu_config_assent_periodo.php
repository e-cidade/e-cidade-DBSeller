<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class M21424MenuConfigAssentPeriodo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<SQL
            update db_itensmenu set id_item = 8047 , descricao = 'Assentamentos Por Período' , help = 'Assentamentos Por Período' , itemativo = '1' , manutencao = '1' , desctec = 'Assentamentos Por Período' , libcliente = 'true' where id_item = 8047;

            insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 228926 ,'Emitir' ,'Emitir relatório de Assentamento por Período' ,'rec2_relassentaporperiodo001.php' ,'1' ,'1' ,'Emitir relatório de Assentamento por Período.' ,'true' );
            insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 8047 ,228926 ,1 ,2323 );

            insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 228927 ,'Configuração' ,'Configuracao do Relatório de Assentamentos por Periodo' ,'web/recursos-humanos/rh/relatorios/assentamento-por-periodo/configuracao' ,'1' ,'1' ,'Configuracao do Relatório de Assentamentos por Periodo' ,'true' );
            insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 8047 ,228927 ,2 ,2323 );
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
            delete from db_menu where id_item_filho = 228927 AND modulo = 2323;
            delete from db_itensmenu where id_item = 228927;

            delete from db_menu where id_item_filho = 228926 AND modulo = 2323;
            delete from db_itensmenu where id_item = 228926;

            update db_itensmenu set funcao = 'rec2_relassentaporperiodo001.php' where id_item = 8047;
SQL;

        DB::connection()->getPdo()->exec($sql);
    }
}
