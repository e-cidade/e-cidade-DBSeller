<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class M25365LiberaRotinasIsencaoCliente extends Migration
{
    public function up()
    {
        $sql = <<<SQL
        update db_itensmenu set libcliente = 'true' where id_item in (5753,5754,5755,5756,5757,5758,5759);
SQL;

        $this->executeQuery($sql);
    }

    public function down()
    {
        $sql = <<<SQL
        update db_itensmenu set libcliente = 'false' where id_item in (5753,5754,5755,5756,5757,5758,5759);
SQL;

        $this->executeQuery($sql);
    }

    private function executeQuery($sql)
    {
        DB::connection()->getPdo()->exec($sql);
    }
}
