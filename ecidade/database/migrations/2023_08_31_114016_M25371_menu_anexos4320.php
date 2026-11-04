<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M25371MenuAnexos4320 extends Migration
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
values (228968, 'Anexos da lei 4320' ,'Anexos da lei 4320' ,'' ,'1' ,'1' ,'anexos da lei 4320' ,'true' ),
       (228969, 'Receita' ,'Receita' ,'pla2_anexos4320receita.php?tipo=LOA' ,'1' ,'1' ,'Anexos da receita ' ,'true' );

insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo )
values ( 228363 ,228968 ,7 ,228358 ),
       ( 228968 ,228969 ,1 ,228358 );
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
delete from db_menu where id_item_filho in (228968, 228969);
delete from db_itensmenu where id_item in (228968, 228969);
SQL
        );
    }
}
