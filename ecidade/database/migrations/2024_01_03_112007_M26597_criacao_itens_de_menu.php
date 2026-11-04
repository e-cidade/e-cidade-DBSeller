<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M26597CriacaoItensDeMenu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
            insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 229021 ,'Implantação de Saldo Por Planilha' ,'Permite a movimentação de implantação através de importação de planilha' ,'web/patrimonial/material/procedimentos/implantacao-saldo-planilha' ,'1' ,'1' ,'Permite a movimentação de implantação através de importação de planilha' ,'true');
            insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 32 ,229021 ,582 ,480 );
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
            delete from db_menu where id_item_filho = 229021 AND modulo = 480;
            delete from db_itensmenu where id_item = 229021;
SQL
        );
    }
}
