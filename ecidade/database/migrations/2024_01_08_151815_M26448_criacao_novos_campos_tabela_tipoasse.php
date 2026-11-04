<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M26448CriacaoNovosCamposTabelaTipoasse extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<SQL

            insert into configuracoes.db_syscampo values(1015581,'h12_lancamentomensal','int4','Quantidades de Lançamentos Mensais para o Assentamento','0', 'Lançamento Mensal',10,'t','f','f',1,'text','Lançamento Mensal');
            insert into configuracoes.db_syscampo values(1015582,'h12_lancamentoanual','int4','Quantidade de Lançamentos Anuais para o Assentamento','0', 'Lançamento Anual',10,'t','f','f',1,'text','Lançamento Anual');
            insert into configuracoes.db_syscampo values(1015583,'h12_bloqueioassentamento','bool','Campo que habilitado abre dois novos campos que geram limite de lançamento de assentamentos para o servidor','f', 'Bloqueio de Assentamentos',1,'t','f','f',5,'text','Bloqueio de Assentamentos');

            insert into configuracoes.db_sysarqcamp values(596,1015581,19,0);
            insert into configuracoes.db_sysarqcamp values(596,1015582,20,0);
            insert into configuracoes.db_sysarqcamp values(596,1015583,21,0);

            alter table recursoshumanos.tipoasse
                add column h12_lancamentomensal integer;
            alter table recursoshumanos.tipoasse
                add column h12_lancamentoanual integer;
            alter table recursoshumanos.tipoasse
                add column h12_bloqueioassentamento boolean default false;

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

            delete from configuracoes.db_sysarqcamp where codarq = 596 and codcam in (1015581,1015582,1015583);
            delete from configuracoes.db_syscampo where codcam in (1015581,1015582,1015583);

            alter table recursoshumanos.tipoasse drop column h12_lancamentomensal;
            alter table recursoshumanos.tipoasse drop column h12_lancamentoanual;
            alter table recursoshumanos.tipoasse drop column h12_bloqueioassentamento;

SQL;
        DB::connection()->getPdo()->exec($sql);
    }
}
