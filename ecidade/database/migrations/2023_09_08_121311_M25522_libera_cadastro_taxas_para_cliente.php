<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class M25522LiberaCadastroTaxasParaCliente extends Migration
{
    public function up()
    {
        $sql = <<<SQL
        update db_itensmenu set libcliente = 'true' where id_item in (228244,228262,228263);
SQL;

        $this->execute($sql);
    }

    public function down()
    {
    }

    private function execute($sql)
    {
        DB::connection()->getPdo()->exec($sql);
    }
}