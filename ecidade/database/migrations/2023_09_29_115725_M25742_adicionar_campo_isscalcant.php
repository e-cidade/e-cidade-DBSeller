<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class M25742AdicionarCampoIsscalcant extends Migration
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
        insert into db_syscampo values(1015477,'q15_valorisencao','float4','Valor da isenção','0', 'Valor isenção',8,'f','f','f',4,'text','Valor isenção');
        insert into db_sysarqcamp values(62,1015477,8,0);
SQL;
        $this->execute($sql);
    }

    private function upEstrutura()
    {
        $sql = <<<SQL
        alter table isscalcant add column q15_valorisencao float default 0;
SQL;
        $this->execute($sql);
    }

    private function downDicionario()
    {
        $sql = <<<SQL
        delete from db_sysarqcamp where codarq = 62 and codcam = 1015477;
        delete from db_syscampo where codcam = 1015477 and nomecam = 'q15_valorisencao';
SQL;
        $this->execute($sql);
    }

    private function downEstrutura()
    {
        $sql = <<<SQL
        alter table isscalcant drop column q15_valorisencao;
SQL;
        $this->execute($sql);
    }

    private function execute($sql)
    {
        DB::connection()->getPdo()->exec($sql);
    }
}
