<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class M25365AdicionaCampoIsscalc extends Migration
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
        insert into db_syscampo values(1015332,'q01_valorisencao','float4','Valor da isenção','0', 'Valor isenção',8,'f','f','f',4,'text','Valor isenção');
        insert into db_sysarqcamp values(61,1015332,8,0);
SQL;

        $this->executeQuery($sql);
    }

    private function upEstrutura()
    {
        $sql = <<<SQL
        alter table issqn.isscalc add column q01_valorisencao float default 0;
SQL;

        $this->executeQuery($sql);
    }

    private function downDicionario()
    {
        $sql = <<<SQL
        delete from db_sysarqcamp where codarq = 61 and codcam = 1015332 and seqarq = 8;
        delete from db_syscampo where codcam = 1015332 and nomecam = 'q01_valorisencao';
SQL;

        $this->executeQuery($sql);
}

    private function downEstrutura()
    {
        $sql = <<<SQL
        alter table issqn.isscalc drop column q01_valorisencao;
SQL;

        $this->executeQuery($sql);
    }

    private function executeQuery($sql)
    {
        DB::connection()->getPdo()->exec($sql);
    }
}
