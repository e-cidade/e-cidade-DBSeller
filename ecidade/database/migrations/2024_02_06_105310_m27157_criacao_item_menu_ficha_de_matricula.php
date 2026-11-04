<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M27157CriacaoItemMenuFichaDeMatricula extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 229028 ,'Ficha de Matrícula' ,'Ficha de Matrícula' ,'web/educacao/escola/relatorios/alunos/ficha-matricula' ,'1' ,'1' ,'Emissão de relatório da ficha de matrícula do aluno.' ,'true' );
insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 1101109 ,229028 ,28 ,1100747 );
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
delete from db_menu where id_item_filho = 229028 AND modulo = 1100747;
delete from db_itensmenu where id_item in (229028);
SQL
        );
    }
}
