<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class M25711AdicionarParametroUsaGrupoDeTaxa extends Migration
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

    public function upDicionario()
    {
        $sql = <<<SQL
        insert into db_syscampo values(1015464,'k03_usagrupotaxa','bool','Utilizar grupo de taxas.','f', 'Usa Grupo de Taxa',1,'f','f','f',5,'text','Usa Grupo de Taxa');
        insert into db_sysarqcamp values(318,1015464,78,0);
SQL;

        $this->execute($sql);
    }

    public function upEstrutura()
    {
        $sql = <<<SQL
        alter table numpref add column k03_usagrupotaxa boolean not null default false;
SQL;

        $this->execute($sql);
    }

    public function downDicionario()
    {
        $sql = <<<SQL
        delete from db_sysarqcamp where codarq = 318 and codcam = 1015464;
        delete from db_syscampo where codcam = 1015464 and nomecam = 'k03_usagrupotaxa';
SQL;

        $this->execute($sql);
    }

    public function downEstrutura()
    {
        $sql = <<<SQL
        alter table numpref drop column k03_usagrupotaxa;
SQL;

        $this->execute($sql);
    }

    private function execute($sql)
    {
        DB::connection()->getPdo()->exec($sql);
    }
}
