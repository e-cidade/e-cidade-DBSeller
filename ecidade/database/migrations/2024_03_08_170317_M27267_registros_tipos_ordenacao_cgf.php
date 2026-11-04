<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class M27267RegistrosTiposOrdenacaoCgf extends Migration
{
    public function up()
    {
        $sql = <<<SQL
        insert into tipoordenacaocgf (k240_descricao) values
        ('Numpre'),
        ('Numpar'),
        ('Receita'),
        ('Data de Vencimento'),
        ('Data de Operação'),
        ('Origem'),
        ('Inicial'),
        ('Data Inicial'),
        ('Processo do Foro');
SQL;
        $this->executeQuery($sql);
    }

    public function down()
    {
        $sql = <<<SQL
        delete from tipoordenacaocgf;
SQL;
        $this->executeQuery($sql);
    }

    private function executeQuery($sql)
    {
        DB::connection()->getPdo()->exec($sql);
    }
}
