<?php

use Illuminate\Database\Migrations\Migration;

class M27727Menu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 229255 ,'Gerar Empenhos Folha Conferência' ,'Gerar Empenhos Folha Conferência' ,'rh4_gerarempenhosfolha001.php?contabilidade=S' ,'1' ,'1' ,'Gerar Empenhos Folha Conferência' ,'true' );
insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 3333 ,229255 ,28 ,209 );
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
delete from db_menu where id_item_filho = 229255 AND modulo = 209;
delete from db_itensmenu where id_item = 229255;
SQL
        );
    }
}
