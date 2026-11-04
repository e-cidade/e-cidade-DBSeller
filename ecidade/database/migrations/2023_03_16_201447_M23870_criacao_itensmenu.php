<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M23870CriacaoItensmenu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<SQL
insert into db_itensmenu( id_item ,descricao ,help ,funcao ,itemativo ,manutencao ,desctec ,libcliente ) values ( 228878 ,'Slips gerados por retenção' ,'Slips gerados por retenção' ,'cai2_retencaoreceitaslip001.php' ,'1' ,'1' ,'Slips gerados por retenção' ,'true' );
insert into db_menu( id_item ,id_item_filho ,menusequencia ,modulo ) values ( 30 ,228878 ,851 ,39 );
SQL;
        
        DB::connection()->getPdo()->exec($sql);
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $sql = <<<SQL
delete from db_menu where id_item_filho = 228878;
delete from db_itensmenu where id_item = 228878;
SQL;
        
        DB::connection()->getPdo()->exec($sql);
    }
    
}
