<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M22950PlanoContratacoes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
        insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 228863 ,'Plano de Contratações' ,'Plano de Contratações' ,'pncp1_planocontratacoes001.php' ,'1' ,'1' ,'Plano de Contratações' ,'true' );
        insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 228804 ,228863 ,9 ,228802 );
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
        delete from db_menu where id_item_filho = 228863 AND modulo = 228802;
        delete from db_itensmenu where id_item = 228863;
SQL
        );
    }
}
