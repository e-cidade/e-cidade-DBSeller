<?php

use Illuminate\Database\Migrations\Migration;

class M24418CriaItensMenuBnc extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
            insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 228931 ,'BNC Compras' ,'BNC Compras' ,'bnc4_processos001.php' ,'1' ,'1' ,'Integração com o BNC' ,'false' );
            delete from db_menu where id_item_filho = 228931 AND modulo = 381;
            insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 1818 ,228931 ,144 ,381 );
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
            DELETE FROM db_menu WHERE id_item_filho IN (228931) AND modulo = 381;
            DELETE FROM db_itensmenu WHERE id_item IN (228931);
SQL
        );
    }
}
