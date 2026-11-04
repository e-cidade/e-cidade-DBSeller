<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M27504CriacaoMenuManutencaoAtoJuridicoEmpenho extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 229248 ,'Manutenção Ato Jurídico do Empenho' ,'Manutenção dos dados de Ato Jurídico e Unidade Gestora do Empenho' ,'emp4_manutencaoatojuridicoempenho001.php' ,'1' ,'1' ,'Manutenção dos dados de Ato Jurídico do Empenho' ,'true' );
insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 4021 ,229248 ,16 ,398 );
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
delete from db_menu where id_item_filho = 229248;
delete from db_itensmenu where id_item = 229248; 
SQL
        );
    }
}
