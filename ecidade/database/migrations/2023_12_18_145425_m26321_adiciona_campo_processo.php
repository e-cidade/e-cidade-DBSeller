<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M26321AdicionaCampoProcesso extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<SQL
        insert into configuracoes.db_syscampo values(1015580,'rh256_processo','varchar(21)','Número do processo vinculado.','', 'Processo',21,'t','t','f',0,'text','Processo');
        insert into configuracoes.db_syscampodef values(1015580,'null','');
        insert into configuracoes.db_sysarqcamp values(1010858,1015580,10,0);

        alter table pessoal.rhlocaltrabagentesnocivos add column rh256_processo varchar(21) default null;

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
        delete from configuracoes.db_sysarqcamp where codcam = 1015580;
        delete from configuracoes.db_syscampodef where codcam = 1015580;
        delete from configuracoes.db_syscampo where codcam = 1015580;

        alter table pessoal.rhlocaltrabagentesnocivos drop column rh256_processo;
SQL;
        DB::connection()->getPdo()->exec($sql);
    }
}
