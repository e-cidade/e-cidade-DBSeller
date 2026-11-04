<?php

use Illuminate\Database\Migrations\Migration;

class M26817MenuBalanceteInformacaoComplementar extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 229024 ,'Balancete Verificação por Informação Complementar' ,'Balancete Verificação por Informação Complementar' ,'web/financeiro/contabilidade/relatorios/balancetes/informacao-complementar' ,'1' ,'1' ,'Balancete Verificação por Informação Complementar' ,'true' );
insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 4065 ,229024 ,18 ,209 );
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
delete from db_menu where id_item_filho = 229024 AND modulo = 209;
delete from db_itensmenu where id_item = 229024;
SQL
        );
    }
}
