<?php

use Illuminate\Database\Migrations\Migration;

class M22949CriaItensMenuTermoContrato extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
            insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 228862 ,'Termo de Contrato' ,'Termo de Contrato' ,'pncp1_termocontrato001.php' ,'1' ,'1' ,'Rotina para gerenciamento de termos dos contratos PNCP' ,'true' );
            delete from db_menu where id_item_filho = 228862 AND modulo = 228802;
            insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 228804 ,228862 ,8 ,228802 );
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
            DELETE FROM db_menu WHERE id_item_filho IN (228862) AND modulo = 228802;
            DELETE FROM db_itensmenu WHERE id_item IN (228862);
SQL
        );
    }
}
