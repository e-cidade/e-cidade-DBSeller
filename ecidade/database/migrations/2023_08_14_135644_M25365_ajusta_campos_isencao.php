<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class M25365AjustaCamposIsencao extends Migration
{
    public function up()
    {
        $this->upDicionario();
        $this->upEstrutura();
    }

    public function down()
    {
        $this->downDicionario();
        $this->downEstrutura();
    }

    private function upDicionario()
    {
        $sql = <<<SQL
        -- delete campo v10_dtisen
        delete from db_sysarqcamp where codarq = 1707 and codcam = 9930;
        delete from db_syscampo where codcam = 9930;

        -- novos campos
        insert into db_syscampo values(1015315,'v10_anoinicial','int4','Ano inicial da isenção','0', 'Ano inicial',5,'f','f','f',1,'text','Ano inicial');
        insert into db_syscampo values(1015316,'v10_anofinal','int4','Ano final isenção','0', 'Ano final',5,'f','f','f',1,'text','Ano final');
        insert into db_syscampo values(1015317,'v10_observacao','text','Observação isenção','', 'Observação',1,'f','t','f',0,'text','Observação');

        insert into db_sysarqcamp values(1707,1015317,5,0);
        insert into db_sysarqcamp values(1707,1015316,6,0);
        insert into db_sysarqcamp values(1707,1015315,7,0);
SQL;

        $this->executeQuery($sql);
    }

    private function upEstrutura()
    {
        $sql = <<<SQL
        alter table isencao drop column v10_dtisen;

        alter table isencao add column v10_anoinicial integer;
        alter table isencao add column v10_anofinal integer;
        alter table isencao add column v10_observacao text;
SQL;

        $this->executeQuery($sql);
    }

    private function downDicionario()
    {
        $sql = <<<SQL
        delete from db_sysarqcamp where codarq = 1707 and codcam in (1015315,1015316,1015317);
        delete from db_syscampo where codcam in (1015315,1015316,1015317) and nomecam in ('v10_anoinicial','v10_anofinal','v10_observacao');
SQL;

        $this->executeQuery($sql);
}

    private function downEstrutura()
    {
        $sql = <<<SQL
        alter table isencao add column v10_dtisen date;

        alter table isencao drop column v10_anoinicial;
        alter table isencao drop column v10_anofinal;
        alter table isencao drop column v10_observacao;
SQL;

        $this->executeQuery($sql);
    }

    private function executeQuery($sql)
    {
        DB::connection()->getPdo()->exec($sql);
    }
}
