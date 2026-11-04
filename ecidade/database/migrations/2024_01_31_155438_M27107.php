<?php

use Illuminate\Database\Migrations\Migration;

class M27107 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente )
values ( 229026 ,'Fix Saldo Conta Bancária' ,'Fix Saldo Conta Bancária' ,'con4_fix_saldo_conta_bancaria.php' ,'1' ,'1' ,'asd' ,'false' );
insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 9954 ,229026 ,247 ,209 );
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
delete from db_menu where id_item_filho = 229026 AND modulo = 209;
delete from db_itensmenu where id_item = 229026;
SQL
        );
    }
}
