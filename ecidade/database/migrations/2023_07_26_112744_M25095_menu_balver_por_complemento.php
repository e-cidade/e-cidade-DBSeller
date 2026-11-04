<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M25095MenuBalverPorComplemento extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 228955 ,'Balancete Verificação por Complemento' ,'Balancete Verificação por Complemento' ,'con2_balancete_verificacao_complemento001.php' ,'1' ,'1' ,'Imprime o balancete de verificação por complemento' ,'true' );
insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 4065 ,228955 ,16 ,209 );
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
delete from db_menu where id_item_filho = 228955 AND modulo = 209;
delete from db_itensmenu where id_item = 228955;
SQL
        );
    }
}
