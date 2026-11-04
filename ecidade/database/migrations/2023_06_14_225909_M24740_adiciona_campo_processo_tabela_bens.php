<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M24740AdicionaCampoProcessoTabelaBens extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<SQL
insert into db_syscampo values(1015170,'t52_processo','varchar(50)','Processo','', 'Processo',50,'t','t','f',0,'text','Processo');
insert into db_sysarqcamp values(914,1015170,16,0);

alter table patrimonio.bens add column t52_processo varchar(50) default null;
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
delete from db_sysarqcamp where codcam = 1015170;
delete from db_syscampo where codcam = 1015170;

alter table patrimonio.bens drop column t52_processo;
SQL;
        DB::connection()->getPdo()->exec($sql);
    }
}
