<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class M24494AdicionaParametroJuridicoAutomatizaParcelamentoTaxas extends Migration
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
        insert into db_syscampo values(1015239,'v19_parcelahonorariosmanual','bool','Permite parcelamento de honorários somente com autorização.','f', 'Parcela honorários com autorização',1,'f','f','f',5,'text','Parcela honorários com autorização');
        insert into db_syscampo values(1015240,'v19_parcelacustasmanual','bool','Permite parcelamento de custas somente com autorização','f', 'Parcela custas com autorização',1,'f','f','f',5,'text','Parcela custas com autorização');
        insert into db_sysarqcamp values(2029,1015240,16,0);
        insert into db_sysarqcamp values(2029,1015239,17,0);
SQL;

        $this->executeQuery($sql);
    }

    private function upEstrutura()
    {
        $sql = <<<SQL
        ALTER TABLE parjuridico ADD COLUMN v19_parcelahonorariosmanual BOOLEAN default true;
        ALTER TABLE parjuridico ADD COLUMN v19_parcelacustasmanual BOOLEAN default true;
SQL;

        $this->executeQuery($sql);
    }

    private function downDicionario()
    {
        $sql = <<<SQL
        DELETE FROM db_sysarqcamp WHERE codarq = 2029 and codcam in (1015240, 1015239) and seqarq in (16, 17);
        DELETE FROM db_syscampo WHERE codcam in (1015239, 1015240) and nomecam in ('v19_parcelahonorariosmanual', 'v19_parcelacustasmanual');
SQL;

        $this->executeQuery($sql);
    }

    private function downEstrutura()
    {
        $sql = <<<SQL
        ALTER TABLE parjuridico DROP COLUMN v19_parcelahonorariosmanual;
        ALTER TABLE parjuridico DROP COLUMN v19_parcelacustasmanual;
SQL;

        $this->executeQuery($sql);
    }

    private function executeQuery($sql)
    {
        DB::connection()->getPdo()->exec($sql);
    }
}
