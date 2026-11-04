<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M26386AdicionaCampoComplementarirfDependente extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<SQL

            insert into configuracoes.db_syscampo values(1015587,'rh31_irfcomplementar','bool','Dependente Complementar somente para fins de IR Evento S1210','f', 'Dependente Complementar somente para IR',1,'f','f','f',5,'text','Dependente Complementar somente para IR');
            insert into configuracoes.db_syscampodef values(1015587,'false','');
            insert into configuracoes.db_sysarqcamp values(1186,1015587,12,0);
            alter table pessoal.rhdepend
                add column rh31_irfcomplementar boolean default false;

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
            delete from configuracoes.db_sysarqcamp where codcam = 1015587;
            delete from configuracoes.db_syscampodef where codcam = 1015587;
            delete from configuracoes.db_syscampo where codcam = 1015587;
            alter table pessoal.rhdepend drop column rh31_irfcomplementar;
SQL;
        DB::connection()->getPdo()->exec($sql);
    }
}
