<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class M25365AdicionarObservacaoIssqnIsencao extends Migration
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
        insert into db_syscampo values(1015314,'v11_observacao','text','Observação isenção','', 'Observação',1,'f','t','f',0,'text','Observação');
        delete from db_sysarqcamp where codarq = 1709;
        insert into db_sysarqcamp values(1709,9928,1,661);
        insert into db_sysarqcamp values(1709,9933,2,0);
        insert into db_sysarqcamp values(1709,9934,3,0);
        insert into db_sysarqcamp values(1709,1015314,4,0);
SQL;

        $this->executeQuery($sql);
    }

    private function upEstrutura()
    {
        $sql = <<<SQL
        alter table isencaotipo add column v11_observacao text;
SQL;

        $this->executeQuery($sql);
    }

    private function downDicionario()
    {
        $sql = <<<SQL
        delete from db_sysarqcamp where codarq = 1709 and codcam = 1015314 and seqarq = 4;
        delete from db_syscampo where codcam = 1015314 and nomecam = 'v11_observacao';
SQL;

        $this->executeQuery($sql);
}

    private function downEstrutura()
    {
        $sql = <<<SQL
        alter table isencaotipo drop column v11_observacao;
SQL;

        $this->executeQuery($sql);
    }

    private function executeQuery($sql)
    {
        DB::connection()->getPdo()->exec($sql);
    }
}
