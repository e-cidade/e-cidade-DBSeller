<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class M28705PrevEncargosTributariosMensais extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<SQL
        insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ,api ) values ( 229359 ,'Relação Previdência e Encargos Tributários Mensais' ,'Relação Previdência e Encargos Tributários Mensais' ,'web/recursos-humanos/pessoal/financeiro/previdencia' ,'1' ,'1' ,'Relação Previdência e Encargos Tributários Mensais' ,'true' ,'false' );
        delete from db_menu where id_item_filho = 229359 AND modulo = 952;
        insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 1797 ,229359 ,64 ,952 );
        insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 2458 ,229359 ,34 ,952 );
SQL;
        $this->execute($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $sql = <<<SQL
        delete from db_menu where id_item_filho = 229359;
        delete from db_itensmenu where id_item = 229359;
SQL;
    $this->execute($sql);
    }

    private function execute($sql)
    {
        DB::connection()->getPdo()->exec($sql);
    }
}
