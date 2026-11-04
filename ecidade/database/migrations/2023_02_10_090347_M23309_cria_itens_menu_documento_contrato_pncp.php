<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class M23309CriaItensMenuDocumentoContratoPncp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
            insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 228853 ,'Inclusão de documento' ,'Inclusão de documento' ,'pncp1_documentocontrato001.php' ,'1' ,'1' ,'Inclusão de documento contratual ao PNCP' ,'true' );
            delete from db_menu where id_item_filho = 228853 AND modulo = 228802;
            insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 228848 ,228853 ,5 ,228802 );

            insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 228859 ,'Exclusão' ,'Exclusão' ,'pncp1_contrato003.php' ,'1' ,'1' ,'Exclusão de contratos do PNCP' ,'true' );
            delete from db_menu where id_item_filho = 228859 AND modulo = 228802;
            insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 228848 ,228859 ,6 ,228802 );
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
            DELETE FROM db_menu WHERE id_item_filho IN (228853, 228859) AND modulo = 228802;
            DELETE FROM db_itensmenu WHERE id_item IN (228853, 228859);
SQL
        );
    }
}
