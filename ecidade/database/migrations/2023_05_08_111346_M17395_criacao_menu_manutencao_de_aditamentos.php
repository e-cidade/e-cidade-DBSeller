<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M17395CriacaoMenuManutencaoDeAditamentos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 228917 ,'Manutenção de Aditamentos' ,'https://e-cidade.wiki.br/patrimonial/contratos/#!procedimentos_aditamentos.md' ,'ac04_aditamentoaltera.php' ,'1' ,'1' ,'Manutenção de Aditamentos' ,'true' );
insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 32 ,228917 ,575 ,8251 );
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
delete from db_menu where id_item_filho = 228917 AND modulo = 8251;
delete from db_itensmenu where id_item = 228917;
SQL
        );
    }
}
