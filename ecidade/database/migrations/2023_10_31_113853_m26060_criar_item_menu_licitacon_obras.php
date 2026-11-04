<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M26060CriarItemMenuLicitaconObras extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
            insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 228991 ,'LicitaCon Obras TCE/RS' ,'LicitaCon Obras TCE/RS' ,'' ,'1' ,'1' ,'Funcionalidades do Licitacon Obras' ,'false' );
            delete from db_menu where id_item_filho = 228991 AND modulo = 381;
            insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 10204 ,228991 ,4 ,381 );

            insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 228993 ,'Parâmetros' ,'Parâmetros' ,'web/patrimonial/licitacoes/licitacon/parametros' ,'1' ,'1' ,'Parâmetros do Licitacon Obras' ,'false' );
            delete from db_menu where id_item_filho = 228993 AND modulo = 381;
            insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 228991 ,228993 ,2 ,381 );

            insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 228994 ,'Inclusão' ,'Inclusão de Obra' ,'web/patrimonial/licitacoes/licitacon/obras/incluir' ,'1' ,'1' ,'Inclusão de Obras Licitacon TCE/RS' ,'false' );
            delete from db_menu where id_item_filho = 228994 AND modulo = 381;
            insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 228991 ,228994 ,4 ,381 );
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
        delete from db_menu where id_item_filho in (228991, 228993, 228994) AND modulo = 381;
        delete from db_itensmenu where id_item in (228991, 228993, 228994);
SQL
        );
    }
}
