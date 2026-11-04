<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class M25430InsereParametroCalculoBaixoRiscoParissqn extends Migration
{
    public function up()
    {
        $this->upDicionario();
        $this->upEstrutura();
    }

    private function upDicionario()
    {
        $sql = <<<SQL
        insert into db_syscampo values(1015339,'q60_unificataxa','bool','Unifica Taxa','f', 'Unifica Taxa',1,'f','f','f',5,'text','Unifica Taxa');
        insert into db_sysarqcamp values(664,1015339,39,0);
SQL;

        $this->execute($sql);
    }

    private function upEstrutura()
    {
        $sql = <<<SQL
        alter table parissqn add column q60_unificataxa boolean not null default false;
SQL;

        $this->execute($sql);
    }

    public function down()
    {
        $this->downDicionario();
        $this->downEstrutura();
    }

    private function downDicionario()
    {
        $sql = <<<SQL
        delete from db_sysarqcamp where codarq = 664 and codcam = 1015339;
        delete from db_syscampo where codcam = 1015339 and nomecam = 'q60_unificataxa';
SQL;

        $this->execute($sql);
    }

    private function downEstrutura()
    {
        $sql = <<<SQL
        alter table parissqn drop column q60_unificataxa;
SQL;

        $this->execute($sql);
    }

    private function execute($sql)
    {
        DB::connection()->getPdo()->exec($sql);
    }
}
