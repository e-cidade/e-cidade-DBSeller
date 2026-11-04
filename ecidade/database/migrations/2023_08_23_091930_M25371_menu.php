<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M25371Menu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values
    ( 228964 ,'Balancete Receita' ,'Balancete Receita' ,'con2_balancete_receita001.php' ,'1' ,'1' ,'Balancete Receita' ,'true' ),
    ( 228966 ,'Resumo da Receita Anexo 2 a partir de 2023' ,'Resumo da Receita Anexo 2 a partir de 2023' ,'orc2_anexodoisresumoreceita001.php' ,'1' ,'1' ,'Resumo da Receita Anexo 2 a partir de 2023' ,'true' );

insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo )
values ( 4065 ,228964 ,17 ,209 ),
       ( 3221 ,228966 ,4 ,116 );
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
delete from db_menu where id_item_filho in (228964, 228966);
delete from db_itensmenu where id_item in (228964, 228966);
SQL
        );
    }
}
