<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class M24494AdicionaParametroTaxaPermiteParcelamento extends Migration
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
        insert into db_syscampo values(1015237,'ar36_permiteparcelamento','bool','Permite parcelamento de taxa','f', 'Permite parcelamento',1,'f','f','f',5,'text','Permite parcelamento');
        insert into db_sysarqcamp values(3221,1015237,13,0);
SQL;

        $this->executeQuery($sql);
    }

    private function upEstrutura()
    {
        $sql = <<<SQL
        ALTER TABLE taxa ADD COLUMN ar36_permiteparcelamento BOOLEAN default false;
SQL;

        $this->executeQuery($sql);
    }

    private function downDicionario()
    {
        $sql = <<<SQL
        DELETE FROM db_sysarqcamp WHERE codarq = 3221 and codcam = 1015237 and seqarq = 13;
        DELETE FROM db_syscampo WHERE codcam = 1015237 and nomecam = 'ar36_permiteparcelamento';
SQL;

        $this->executeQuery($sql);
    }

    private function downEstrutura()
    {
        $sql = <<<SQL
        ALTER TABLE taxa DROP COLUMN ar36_permiteparcelamento;
SQL;

        $this->executeQuery($sql);
    }

    private function executeQuery($sql)
    {
        DB::connection()->getPdo()->exec($sql);
    }
}
