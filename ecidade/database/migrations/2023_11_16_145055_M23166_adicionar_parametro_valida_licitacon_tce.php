<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M23166AdicionarParametroValidaLicitaconTce extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
            insert into db_syscampo values(1015549,'l12_validalicitacon','int4','Permite a emissão do empenho com ou sem aviso, ou não permite com aviso caso a licitação não encontra-se no portal LICITACON TCE/RS.','0', 'Valida Licitacon',4,'f','f','f',1,'text','Valida Licitacon');
            insert into db_syscampodef values(1015549,'1','');
            insert into db_syscampodef values(1015549,'2','');
            insert into db_syscampodef values(1015549,'3','');
            insert into db_sysarqcamp values(2055,1015549,9,0);
            alter table licitaparam
            add column l12_validalicitacon integer default 1;
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
            delete from db_sysarqcamp where codarq = 2055 and codcam = 1015549;
            delete from db_syscampodef where codcam = 1015549;
            delete from db_syscampo where codcam = 1015549;
            alter table licitaparam drop column l12_validalicitacon;
SQL
        );
    }
}
