<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M18884MenuInserirResultadoItem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 228821 ,'Inserir Resultado' ,'Inserir Resultado' ,'pncp1_resultadoitem001.php' ,'1' ,'1' ,'Inserir resultado dos itens na compra do PNCP' ,'true' );
insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 228816 ,228821 ,2 ,228802 );

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
delete from db_itensmenu where id_item = 228821;
delete from db_menu where id_item_filho = 228821 AND modulo = 228802;
SQL
        );
    }
}
