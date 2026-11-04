<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M25095MenuCriaRecursosLancamento extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 228956 ,'Manutenção dos Lançamentos sem recursos' ,'Manutenção dos Lançamentos sem recursos' ,'con4_lancamentos_sem_recursos.php' ,'1' ,'1' ,'Inclui recursos para os lançamentos que não tem recurso.' ,'true' );
insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 9954 ,228956 ,245 ,209 );
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
delete from db_menu where id_item_filho = 228956 AND modulo = 209;
delete from db_itensmenu where id_item = 228956;
SQL
        );
    }
}
