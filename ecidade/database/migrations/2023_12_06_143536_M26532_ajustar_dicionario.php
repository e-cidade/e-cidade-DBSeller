<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class M26532AjustarDicionario extends Migration
{

    public function up()
    {
        $sql = <<<SQL
        update db_syscampo set
         descricao = 'Permite construção sem ano',
         rotulo = 'Permite construção sem ano',
         rotulorel = 'Permite construção sem ano'
            where nomecam = 'j18_validarano';
SQL;
        $this->executeQuery($sql);
    }

    public function down()
    {
        return;
    }

    private function executeQuery($sql)
    {
        DB::connection()->getPdo()->exec($sql);
    }
}
