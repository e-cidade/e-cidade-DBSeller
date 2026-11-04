<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M26220AdicionaCampoDescricaoDependente extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<SQL
            insert into configuracoes.db_syscampo values(1015572,'rh31_descricaoparentesco','varchar(100)','Descrição do Parentesco','', 'Descrição do Parentesco',100,'t','t','f',0,'text','Descrição do Parentesco');
            insert into configuracoes.db_sysarqcamp values(1186,1015572,11,0);
            alter table pessoal.rhdepend
                add column rh31_descricaoparentesco varchar(100) default null;

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
            delete from configuracoes.db_sysarqcamp where codcam = 1015572;
            delete from configuracoes.db_syscampo where codcam = 1015572;
            alter table pessoal.rhdepend drop column rh31_descricaoparentesco;
SQL;
        DB::connection()->getPdo()->exec($sql);
    }
}
