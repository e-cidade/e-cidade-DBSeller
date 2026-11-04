<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M22723MenuAtaRegistroPreco extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<sql
insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 228858 ,'Ata de Registro de Preço' ,'Ata de Registro de Preço' ,'pncp1_ataregistropreco001.php' ,'1' ,'1' ,'Inclusão da Ata de Registro de Preço' ,'true' );
insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 228804 ,228858 ,7 ,228802 );
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
    delete from db_menu where id_item_filho = 228858 AND modulo = 228802;
    delete from db_itensmenu where id_item = 228858;
sql
        );
    }
}
