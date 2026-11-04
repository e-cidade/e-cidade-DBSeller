<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class M18884CriaItensMenuPncpContrato extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<sql
            insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 228848 ,'Contrato' ,'Contrato' ,'' ,'1' ,'1' ,'Contratos Portal Nacional de Contratações Públicas' ,'true' );
            delete from db_menu where id_item_filho = 228848 AND modulo = 228802;
            insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 228804 ,228848 ,5 ,228802 );

            insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 228849 ,'Inclusão' ,'Inclusão' ,'pncp1_contrato001.php' ,'1' ,'1' ,'Inclusão de contrato PNCP' ,'true' );
            delete from db_menu where id_item_filho = 228849 AND modulo = 228802;
            insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 228848 ,228849 ,1 ,228802 );
sql
        );

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::connection()->getPdo()->exec(<<<sql
            DELETE FROM db_menu WHERE id_item_filho IN (228848, 228849) AND modulo = 228802;
            DELETE FROM db_itensmenu WHERE id_item IN (228848, 228849);
sql
        );

    }
}
