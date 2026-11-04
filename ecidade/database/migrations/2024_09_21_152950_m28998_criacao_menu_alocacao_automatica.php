<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M28998CriacaoMenuAlocacaoAutomatica extends Migration
{
    private function upDicionario()
    {
        DB::connection()->getPdo()->exec(<<<SQL
insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ,api ) values ( 229306 ,' Alocação automática' ,' Alocação automática' ,'edu4_alocacaoautomatica.php' ,'1' ,'1' ,' Alocação automática' ,'true' ,'false' );
insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 228935 ,229306 ,1 ,228923 );
SQL
        );
        
    }

    private function downDicionario()
    {
        DB::connection()->getPdo()->exec(<<<SQL
delete from db_menu where id_item_filho = 229306 AND modulo = 228923;
delete from db_itensmenu where id_item = 229306;
SQL
        );
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->upDicionario();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->downDicionario();
    }
}
