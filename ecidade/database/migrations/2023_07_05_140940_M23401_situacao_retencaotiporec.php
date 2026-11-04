<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M23401SituacaoRetencaotiporec extends Migration
{

        /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<SQL
insert into db_syscampo values(1015227,'e21_ativo','bool','Se registro esta ativo','t', 'Ativo',1,'f','f','f',5,'text','Ativo');
insert into db_syscampodef values(1015227,'t','SIM');
insert into db_syscampodef values(1015227,'f','NÃO');
insert into db_sysarqcamp values(2112,1015227,11,0);

alter table empenho.retencaotiporec add column e21_ativo boolean default true;
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
delete from db_sysarqcamp where codcam = 1015227;
delete from db_syscampodef where codcam = 1015227;
delete from db_syscampo where codcam = 1015227;

alter table empenho.retencaotiporec drop column e21_ativo;
SQL;
        DB::connection()->getPdo()->exec($sql);
    }
}
