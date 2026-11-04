<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M28975MenuRelatorioFuncioanrioAtivos extends Migration
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
    values ( 3000340 ,'Funcionários Ativos' ,'Funcionários Ativos' ,'edu2_funcionariosativos.php' ,'1' ,'1' ,'Relatório de Funcionários Ativos por período' ,'true' );
insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 1101111 ,3000340 ,15 ,1100747 );
insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 1101111 ,3000340 ,16 ,7159 );
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
delete from db_menu where id_item = 1101111 and id_item_filho = 3000340;
delete from db_itensmenu where id_item = 3000340;
SQL
        );
    }
}
