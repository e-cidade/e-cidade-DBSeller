<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class M25522AdicionarCamposTabelaTaxaslancadas extends Migration
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
        insert into db_syscampo values(1015369,'ar44_permitesubtaxas','bool','Permite Subtaxas','f', 'Permite Subtaxas',1,'f','f','f',5,'text','Permite Subtaxas');
        insert into db_sysarqcamp values(1010547,1015369,15,0);
SQL;

        $this->execute($sql);
    }

    private function upEstrutura()
    {
        $sql = <<<SQL
        alter table arrecadacao.taxaslancadas add column ar44_permitesubtaxas boolean not null default false;
SQL;

        $this->execute($sql);
    }

    private function downDicionario()
    {
        $sql = <<<SQL
        delete from db_sysarqcamp where codarq = 1010547 and codcam = 1015369 and seqarq = 15;
        delete from db_syscampo where codcam = 1015369 and  nomecam = 'ar44_permitesubtaxas';
SQL;

        $this->execute($sql);
    }

    private function downEstrutura()
    {
        $sql = <<<SQL
        alter table arrecadacao.taxaslancadas drop column ar44_permitesubtaxas;
SQL;

        $this->execute($sql);
    }

    private function execute($sql)
    {
        DB::connection()->getPdo()->exec($sql);
    }
}
