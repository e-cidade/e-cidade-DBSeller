<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M27972CriaItemMenuImportacaoContratacao extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
            insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 229269 ,'Importação' ,'Importação de contratação' ,'pncp4_importacaocompraeditalaviso001.php' ,'1' ,'1' ,'Importa contratação enviada ao PNCP por terceiros.' ,'true' );
            delete from db_menu where id_item_filho = 229269 AND modulo = 228802;
            insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 228816 ,229269 ,6 ,228802 );

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
            DELETE FROM db_menu WHERE id_item_filho IN (229269) AND modulo = 228802;
            DELETE FROM db_itensmenu WHERE id_item IN (229269);
SQL
        );
    }
}
