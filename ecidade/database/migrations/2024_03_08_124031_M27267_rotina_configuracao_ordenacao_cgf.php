<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class M27267RotinaConfiguracaoOrdenacaoCgf extends Migration
{
    public function up()
    {
        $sql = <<<SQL
insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 229036 ,'Ordenação CGF' ,'Ordenação CGF' ,'' ,'1' ,'1' ,'Rotina de configuração dos parâmetros de ordenação da CGF.' ,'true' );
update db_itensmenu set id_item = 229036 , descricao = 'Ordenação CGF' , help = 'Ordenação CGF' , funcao = 'web/tributario/arrecadacao/parametros/config-ordenacao-cfg' , itemativo = '1' , manutencao = '1' , desctec = 'Rotina de configuração dos parâmetros de ordenação da CGF.' , libcliente = 'true' where id_item = 229036;
delete from db_menu where id_item_filho = 229036 AND modulo = 1985522;
insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 5383 ,229036 ,8 ,1985522 );
update db_itensmenu set id_item = 229036 , descricao = 'Ordenação CGF' , help = 'Ordenação CGF' , funcao = 'web/tributario/arrecadacao/parametros/config-ordenacao-cgf' , itemativo = '1' , manutencao = '1' , desctec = 'Rotina de configuração dos parâmetros de ordenação da CGF.' , libcliente = 'true' where id_item = 229036;
SQL;
        $this->executeQuery($sql);
    }

    public function down()
    {
        $sql = <<<SQL
delete from db_menu where id_item_filho = 229036 AND modulo = 1985522;
delete from db_itensmenu where id_item = 229036 and descricao = 'Ordenação CGF';
SQL;
        $this->executeQuery($sql);
    }

    private function executeQuery($sql)
    {
        DB::connection()->getPdo()->exec($sql);
    }
}
