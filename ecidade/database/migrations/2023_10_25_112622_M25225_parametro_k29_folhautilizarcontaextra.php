<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M25225ParametroK29Folhautilizarcontaextra extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
insert into db_syscampo values(1015517,'k29_folhautilizarcontaextra','bool','Conta Extra - Planilha e Slip da folha','t', 'Conta Extra - Planilha e Slip da folha',1,'f','f','f',5,'text','Conta Extra - Planilha e Slip da folha');
insert into db_sysarqcamp values(1503,1015517,18,0);
alter table caixa.caiparametro add column k29_folhautilizarcontaextra boolean default false;
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
delete from db_sysarqcamp where codcam = 1015517;
delete from db_syscampo where codcam = 1015517;

alter table caixa.caiparametro drop column k29_folhautilizarcontaextra;
SQL
        );
    }
}
