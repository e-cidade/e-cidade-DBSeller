<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class M26817CreateMenuMapSaldoDeRPPorEmpenho extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 229023 ,'Mapeamento Saldo de RP por empenho' ,'Mapeamento Empenho RP Conta' ,'web/financeiro/contabilidade/procedimento/mapeamento-empenho-rp-conta' ,'1' ,'1' ,'Mapeamento EmpenhoRP Conta' ,'true' );");
        DB::statement("insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 9954 ,229023 ,246 ,209)");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("delete from db_menu where id_item_filho = 229023 AND modulo = 209");
        DB::statement("delete from db_itensmenu where id_item = 229023");
    }
}
