<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class M25828AdicionaModImprime81Recibo extends Migration
{
    public function up()
    {
        $sql = <<<SQL
        insert into cadmodcarne (k47_sequencial,k47_descr, k47_obs,k47_altura,k47_orientacao,k47_tipoconvenio) values (81,'RECIBO COM HISTORICO DIMINUIDO', null,0,null,null)
SQL;

        $this->execute($sql);
    }

    public function down()
    {
        $sql = <<<SQL
        delete from cadmodcarne where k47_sequencial = 81;
SQL;

        $this->execute($sql);
    }

    private function execute($sql)
    {
        DB::connection()->getPdo()->exec($sql);
    }
}
