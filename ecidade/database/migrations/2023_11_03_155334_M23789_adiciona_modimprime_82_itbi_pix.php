<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class M23789AdicionaModimprime82ItbiPix extends Migration
{
    public function up()
    {
        $sql = <<<SQL
        insert into cadmodcarne (k47_sequencial,k47_descr, k47_obs,k47_altura,k47_orientacao,k47_tipoconvenio) values (82,'GUIA ITBI PIX', null,0,null,null)
SQL;

        $this->execute($sql);
    }

    public function down()
    {
        $sql = <<<SQL
        delete from cadmodcarne where k47_sequencial = 82;
SQL;

        $this->execute($sql);
    }

    private function execute($sql)
    {
        DB::connection()->getPdo()->exec($sql);
    }
}
