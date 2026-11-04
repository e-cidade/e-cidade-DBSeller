<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class M26515AdicionaParametroPermiteInscricaoEconomicaCgf extends Migration
{
    public function up()
    {
        $this->upEstrutura();
        $this->upDicionario();
    }

    public function down()
    {
        $this->downEstrutura();
        $this->downDicionario();
    }

    private function upEstrutura()
    {
        $sql = <<<SQL
        alter table configuracoes.db_config add column db21_permiteinscricaocgf boolean default true;
        select configuracoes.fc_auditoria_cria_funcao('configuracoes.db_config');
SQL;
        $this->executeQuery($sql);
    }

    private function upDicionario()
    {
        $sql = <<<SQL
        insert into db_syscampo values(1015574,'db21_permiteinscricaocgf','bool','Permite ou não acessar a consulta geral financeira informando o número da inscrição econômica.','f', 'Permite inscrição econômica CGF',1,'f','f','f',5,'text','Permite inscrição econômica CGF');
        insert into db_sysarqcamp values(83,1015574,57,0);
SQL;
        $this->executeQuery($sql);
    }

    private function downEstrutura()
    {
        $sql = <<<SQL
        alter table configuracoes.db_config drop column db21_permiteinscricaocgf;
SQL;
        $this->executeQuery($sql);
    }

    private function downDicionario()
    {
        $sql = <<<SQL
        delete from db_sysarqcamp where codarq = 83 and codcam = 1015574;
        delete from db_syscampo where codcam = 1015574 and nomecam = 'db21_permiteinscricaocgf';
SQL;
        $this->executeQuery($sql);
    }

    private function executeQuery($sql)
    {
        DB::connection()->getPdo()->exec($sql);
    }
}
