<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M25125CriacaoMenuBoletimDesempenhoPcd extends Migration
{
    private function upDicionario()
    {
        DB::connection()->getPdo()->exec(<<<SQL
insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 229274 ,'Turmas/AEE' ,'Turmas/AEE' ,'' ,'1' ,'1' ,'Turmas/AEE' ,'true' );
insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 4964 ,229274 ,26 ,1100747 );
insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 229275 ,'Turmas/AEE' ,'Turmas/AEE' ,'' ,'1' ,'1' ,'Turmas/AEE' ,'true' );
insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 4964 ,229275 ,27 ,7159 );
insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 229276 ,'Boletim de Desempenho' ,'Boletim de Desempenho' ,'web/educacao/secretaria/relatorios/turmas/boletim-desempenho-pcd' ,'1' ,'1' ,'Boletim de Desempenho' ,'true' );
insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 229274 ,229276 ,1 ,1100747 );
insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 229277 ,'Boletim de Desempenho' ,'Boletim de Desempenho' ,'web/educacao/secretaria/relatorios/turmas/boletim-desempenho-pcd' ,'1' ,'1' ,'Boletim de Desempenho' ,'true' );
insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 229275 ,229277 ,1 ,7159 );
SQL
        );
    }

    private function downDicionario()
    {
        DB::connection()->getPdo()->exec(<<<SQL
delete from db_menu where id_item_filho = 229274 AND modulo = 1100747;
delete from db_itensmenu where id_item = 229274;
delete from db_menu where id_item_filho = 229275 AND modulo = 7159;
delete from db_itensmenu where id_item = 229275;
delete from db_menu where id_item_filho = 229276 AND modulo = 1100747;
delete from db_itensmenu where id_item = 229276;
delete from db_menu where id_item_filho = 229277 AND modulo = 7159;
delete from db_itensmenu where id_item = 229277;
SQL
        );
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->upDicionario();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->downDicionario();
    }
}
