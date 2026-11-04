<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class M27815AdicionarParametroArrecadacaoCustasHonorarios extends Migration
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

    public function upEstrutura()
    {
        $sql = <<<SQL
        alter table caixa.numpref add column k03_custashonorarios boolean default true;
SQL;
        $this->execute($sql);
    }

    public function downEstrutura()
    {
        $sql = <<<SQL
        alter table caixa.numpref drop column k03_custashonorarios;
SQL;
        $this->execute($sql);
    }

    public function upDicionario()
    {
        $sql = <<<SQL
        insert into db_syscampo values(1015650,'k03_custashonorarios','bool','Habilita custas e honorários','f', 'Habilita custas e honorários',1,'f','f','f',5,'text','Habilita custas e honorários');
        insert into db_sysarqcamp values(318,1015650,83,0);
SQL;
        $this->execute($sql);
    }

    public function downDicionario()
    {
        $sql = <<<SQL
        delete from db_sysarqcamp where codarq = 318 and codcam = 1015650;
        delete from db_syscampo where codcam = 1015650 and nomecam = 'k03_custashonorarios';
SQL;
        $this->execute($sql);
    }

    private function execute($sql)
    {
        DB::connection()->getPdo()->exec($sql);
    }
}
