<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M26326AdicionaCampoDemissaoVoluntaria extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<SQL
            insert into configuracoes.db_syscampo values(1015588,'r59_demissaovoluntaria','bool','Indicativo se o desligamento ocorreu por meio de adesão a Programa de Demissão Voluntária (PDV)','f', 'Programa de Demissão Voluntária',1,'f','f','f',5,'text','Programa de Demissão Voluntária');
            insert into configuracoes.db_syscampodef values(1015588,'f','');
            insert into configuracoes.db_sysarqcamp values(589,1015588,32,0);
            alter table pessoal.rescisao
                add column r59_demissaovoluntaria boolean default false;

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
            delete from configuracoes.db_sysarqcamp where codcam = 1015588;
            delete from configuracoes.db_syscampodef where codcam = 1015588;
            delete from configuracoes.db_syscampo where codcam = 1015588;
            alter table pessoal.rescisao drop column r59_demissaovoluntaria;
SQL;
        DB::connection()->getPdo()->exec($sql);
    }
}
