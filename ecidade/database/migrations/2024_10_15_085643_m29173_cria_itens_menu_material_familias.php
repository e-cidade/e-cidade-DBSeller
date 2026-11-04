<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M29173CriaItensMenuMaterialFamilias extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
        insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ,api ) values ( 229322 ,'Cadastro de Famílias' ,'Cadastro de Famílias' ,'' ,'1' ,'1' ,'Cadastro de Famílias do Material' ,'true' ,'false' );
        delete from db_menu where id_item_filho = 229322 AND modulo = 480;
        insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 3470 ,229322 ,52 ,480 );


        insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ,api ) values ( 229323 ,'Inclusão' ,'Inclusão de Família' ,'mat1_materialfamilia001.php' ,'1' ,'1' ,'Inclusão de Família' ,'true' ,'false' );
        delete from db_menu where id_item_filho = 229323 AND modulo = 480;
        insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 229322 ,229323 ,1 ,480 );


        insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ,api ) values ( 229324 ,'Alteração' ,'Alteração de Família' ,'mat1_materialfamilia002.php' ,'1' ,'1' ,'Alteração de Família' ,'true' ,'false' );
        delete from db_menu where id_item_filho = 229324 AND modulo = 480;
        insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 229322 ,229324 ,2 ,480 );


        insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ,api ) values ( 229325 ,'Exclusão' ,'Exclusão de Família' ,'mat1_materialfamilia003.php' ,'1' ,'1' ,'Exclusão de Família' ,'true' ,'false' );
        delete from db_menu where id_item_filho = 229325 AND modulo = 480;
        insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 229322 ,229325 ,3 ,480 );

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
            delete from db_menu where id_item_filho in (229322, 229323, 229324, 229325) AND modulo = 480;
            delete from db_itensmenu where id_item in (229322, 229323, 229324, 229325);
SQL
        );
    }
}
