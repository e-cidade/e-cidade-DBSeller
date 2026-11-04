<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M23689MenuAnexo12 extends Migration
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
values ( 228869 ,'Anexo XII - Despesas com saúde' ,'Anexo XII - Despesas com saúde' ,'pla2_abas_rreo.php?anexo=12' ,'1' ,'1' ,'Anexo XII - Despesas com saúde' ,'true' );

insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values (228634, 228869, 12, 209);
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
delete from db_menu where id_item_filho = 228869 AND modulo = 209;
delete from db_itensmenu where  id_item = 228869;
SQL
        );
    }
}
