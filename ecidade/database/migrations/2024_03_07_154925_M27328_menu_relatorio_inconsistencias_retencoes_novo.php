<?php

use Illuminate\Database\Migrations\Migration;

class M27328MenuRelatorioInconsistenciasRetencoesNovo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 229032 ,'NFS-e (novo)' ,'NFS-e (novo)' ,'' ,'1' ,'1' ,'NFS-e (novo)' ,'true' );
delete from db_menu where id_item_filho = 229032 AND modulo = 277;
insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 30 ,229032 ,855 ,277 );
insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 229033 ,'Relatório de Inconsistências de Retenções (novo)' ,'Relatório de Inconsistências de Retenções (novo)' ,'fis2_comparativoretencao_novo001.php' ,'1' ,'1' ,'Relatório de Inconsistências de Retenções (novo)' ,'true' );
delete from db_menu where id_item_filho = 229033 AND modulo = 277;
insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 229032 ,229033 ,1 ,277 );
update db_itensmenu set id_item = 229033 , descricao = 'Inconsistências de Retenções (novo)' , help = 'Relatório de Inconsistências de Retenções (novo)' , funcao = 'fis2_comparativoretencao_novo001.php' , itemativo = '1' , manutencao = '1' , desctec = 'Relatório de Inconsistências de Retenções (novo)' , libcliente = 'true' where id_item = 229033;

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
delete from db_itensmenu where id_item = 229032;
delete from db_itensmenu where id_item = 229033;
delete from db_menu where id_item_filho = 229032 AND modulo = 277;
delete from db_menu where id_item_filho = 229033 AND modulo = 277;

SQL
        );
    }
}
