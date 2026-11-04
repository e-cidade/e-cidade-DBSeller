<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class M25702CampoProcessoObrigatorioParcelamentoModuloArrecadacao extends Migration
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
        alter table caixa.numpref add column k03_processoparcelamento boolean not null default false;
        select configuracoes.fc_auditoria_cria_funcao('caixa.numpref');
SQL;
        $this->executeQuery($sql);
    }

    private function upDicionario()
    {
        $sql = <<<SQL
        insert into db_syscampo values(1015557,'k03_processoparcelamento','bool','Habilita campo processo obrigatório para parcelamentos','f', 'Processo Obrigatório Parcelamento',1,'f','f','f',5,'text','Processo Obrigatório Parcelamento');
        insert into db_sysarqcamp values(318,1015557,82,0);
SQL;
        $this->executeQuery($sql);
    }

    private function downEstrutura()
    {
        $sql = <<<SQL
        alter table caixa.numpref drop column k03_processoparcelamento;
SQL;
        $this->executeQuery($sql);
    }

    private function downDicionario()
    {
        $sql = <<<SQL
        delete from db_sysarqcamp where codarq = 318 and codcam = 1015557;
        delete from db_syscampo where codcam = 1015557 and nomecam = 'k03_processoparcelamento';
SQL;
        $this->executeQuery($sql);
    }

    private function executeQuery($sql)
    {
        DB::connection()->getPdo()->exec($sql);
    }
}
