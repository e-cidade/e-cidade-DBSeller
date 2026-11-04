<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class M22245CriacaoItensMenuRelatoriosEducacao extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
        insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 228855 ,'Relatório de turmas cadastradas por escola e ano letivo' , 'Relatório de turmas cadastradas por escola e ano letivo' , 'edu2_turmascadastradasescolaanoletivo001.php' ,'1' ,'1' ,'Relatório de turmas cadastradas por escola e ano letivo ' ,'true' );
        insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 1101110 ,228855 ,40 ,1100747 );
        insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 1101110 ,228855 ,41 ,7159 );

        insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 228856 ,'Lista de Confirmação de Rematricula' ,'Lista de Confirmação de Rematricula' ,'edu2_confirmacaorematricula001.php' ,'1' ,'1' ,'Lista de Confirmação de Rematricula' ,'true' );
        insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 1101112 ,228856 ,20 ,1100747 );
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

        delete from db_itensmenu where id_item=228855;
        delete from db_menu where id_item_filho = 228855 AND modulo = 1100747;
        delete from db_menu where id_item_filho = 228855 AND modulo = 7159;

        delete from db_itensmenu where id_item=228856;
        delete from db_menu where id_item_filho = 228856 AND modulo = 1100747;

SQL
        );
    }
}
