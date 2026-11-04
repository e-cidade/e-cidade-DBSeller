<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M28231Menu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(<<<SQL
insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente )
values (229265 ,'Modelo 5' ,'Modelo 5' ,'web/financeiro/contabilidade/deliberacao/285/modelo-5' ,'1' ,'1' ,'Modelo 5 da deliberaчуo 277' ,'false' ),
       (229266 ,'Deliberaчуo 285' ,'Deliberaчуo 285' ,'' ,'1' ,'1' ,'Deliberaчуo 285' ,'false' );

insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo )
values ( 3331 ,229266 ,62 ,209 ),
       ( 229266 ,229265 ,1 ,209 );
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
delete from db_menu where id_item_filho in (229266,229265)  AND modulo = 209;
delete from db_itensmenu where id_item in (229266,229265);
SQL
        );
    }
}
