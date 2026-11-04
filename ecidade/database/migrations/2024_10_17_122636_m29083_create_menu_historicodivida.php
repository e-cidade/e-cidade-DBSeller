<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M29083CreateMenuHistoricodivida extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("
           insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ,api ) values ( 229326 ,'Lançamento de histórico de dívida' ,'Lançar observações para dívidas' ,'web/tributario/divida-ativa/lancamento-historico' ,'1' ,'1' ,'Lançamento de histórico de dividas em massa' ,'true' ,'false' );
           insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 32 ,229326 ,588 ,81 );
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("
            delete from db_menu where id_item_filho = 229326 AND modulo = 81;
            delete from db_itensmenu where id_item = 229326;
        ");
    }
}
