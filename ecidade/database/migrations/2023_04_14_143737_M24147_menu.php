<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M24147Menu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 228907 ,'Mapeamento Automático Plano de Contas' ,'Mapeamento Automático Plano de Contas' ,'web/financeiro/contabilidade/plano-contas/mapeamento/vinculo-automatico' ,'1' ,'1' ,'Mapeamento Automático Plano de Contas' ,'true' );
insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 9954 ,228907 ,244 ,209 );
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
delete from db_menu where id_item_filho = 228907 AND modulo = 209;
delete from db_itensmenu where id_item = 228907;
SQL
        );
    }
}
