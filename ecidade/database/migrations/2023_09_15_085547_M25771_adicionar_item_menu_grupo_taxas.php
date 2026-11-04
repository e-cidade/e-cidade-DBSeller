<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class M25771AdicionarItemMenuGrupoTaxas extends Migration
{
    public function up()
    {
        $sql = <<<SQL
        insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 228973 ,'Grupo de Taxas' ,'Grupo de Taxas' ,'web/tributario/arrecadacao/cadastros/grupo-de-taxas' ,'1' ,'1' ,'Grupo de taxas' ,'true' );
        insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 29 ,228973 ,317 ,1985522 );
SQL;

        $this->execute($sql);
    }

    public function down()
    {
        $sql = <<<SQL
        delete from db_menu where id_item_filho = 228973 AND modulo = 1985522;
        delete from db_itensmenu where id_item = 228973 and descricao = 'Grupo de Taxas';
SQL;

        $this->execute($sql);
    }

    private function execute($sql)
    {
        DB::connection()->getPdo()->exec($sql);
    }
}
