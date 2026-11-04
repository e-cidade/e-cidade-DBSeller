<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M29552CriarMenu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(<<<SQL
        insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ,api ) values ( 229360 ,'Contas do Exercício MSC' ,'Contas do Exercício MSC' ,'web/financeiro/contabilidade/conta-corrente/cadastro/por-recurso' ,'1' ,'1' ,'Inclui uma conta PCASP para controle de saldo por recurso. ' ,'true' ,'false' );
        insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 29 ,229360 ,321 ,209 );
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
        DB::unprepared(<<<SQL
        delete from db_menu where id_item_filho = 229360 AND modulo = 209;
        delete from db_itensmenu  where id_item =  229360;
SQL
        );
    }
}
