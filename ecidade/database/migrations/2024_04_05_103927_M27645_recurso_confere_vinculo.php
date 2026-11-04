<?php

use Illuminate\Database\Migrations\Migration;

class M27645RecursoConfereVinculo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 229251 ,'Conferência vínculo de recursos nos cadastros' ,'Conferência vínculo de recursos nos cadastros' ,'web/financeiro/orcamento/relatorios/recursos/confere-vinculo' ,'1' ,'1' ,'Conferência vínculo de recursos nos cadastros' ,'true' );
insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 4148 ,229251 ,12 ,116 );

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
delete from db_menu where id_item_filho = 229251 AND modulo = 116;
delete from db_itensmenu where id_item = 229251;
SQL
        );
    }
}
