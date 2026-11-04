<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class M29529ItemMenuConsultaTriagem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(<<<SQL
insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ,api ) values ( 229379 ,'Triagem Laboratorial' ,'Triagem Laboratorial' ,'lab3_triagemlaboratorial001.php' ,'1' ,'1' ,'Triagem Laboratorial' ,'true' ,'false' );
insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 3491 ,229379 ,6 ,8167 );
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
delete from itens_menu_usuario_favoritos where id_item_menu = 229379;
delete from db_menu where id_item_filho = 229379;
delete from db_itensmenu where id_item = 229379;
SQL
    );  
    }
}
