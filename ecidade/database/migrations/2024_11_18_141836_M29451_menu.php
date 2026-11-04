<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M29451Menu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente )
values ( 229341 ,'Relatório de conferência Arquivo Balancete' ,'Relatório de conferência Arquivo Balancete' ,'con2_relatorioarquivosigfisbalancete.php' ,'1' ,'1' ,'Relatório de conferência Arquivo Balancete' ,'true' );
delete from db_menu where id_item_filho = 229341 AND modulo = 209;
insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 9353 ,229341 ,3 ,209 );
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
delete from db_menu where id_item_filho = 229341 AND modulo = 209;
delete from db_itensmenu where id_item = 229341;
SQL
        );
    }
}
