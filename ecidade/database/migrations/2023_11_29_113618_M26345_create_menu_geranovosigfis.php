<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M26345CreateMenuGeranovosigfis extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL

insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 229003 ,'Gerar Novo SIGFIS' ,'Gerar Novo SIGFIS' ,'con4_gerarnovosigfis.php' ,'1' ,'1' ,'Gerar Novo SIGFIS' ,'true' );
insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 8997 ,229003 ,10 ,209 );
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
delete from db_menu where id_item_filho = 229003 AND modulo = 209;
delete from db_itensmenu  where id_item = 229003;
SQL
        );
    }
}
