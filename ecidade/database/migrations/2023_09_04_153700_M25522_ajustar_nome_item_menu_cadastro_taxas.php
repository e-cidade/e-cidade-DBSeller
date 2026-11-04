<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class M25522AjustarNomeItemMenuCadastroTaxas extends Migration
{
    public function up()
    {
        $sql = <<<SQL
        update db_itensmenu set id_item = 228244 , descricao = 'Cadastro de Taxas' , help = 'Inclusão e alteração das taxas lançadadas' , funcao = 'arr4_taxaslancadas.php' , itemativo = '1' , manutencao = '1' , desctec = 'Cadastro das taxas.' , libcliente = 'true' where id_item = 228244;
SQL;

        $this->execute($sql);
    }

    public function down()
    {
        $sql = <<<SQL
        update db_itensmenu set id_item = 228244 , descricao = 'Cadastro de Taxas' , help = 'Inclusão e alteração das taxas lançadadas' , funcao = 'arr4_taxaslancadas.php' , itemativo = '1' , manutencao = '1' , desctec = 'Cadastro das taxas.' , libcliente = 'true' where id_item = 228244;
SQL;

        $this->execute($sql);
    }

    private function execute($sql)
    {
        DB::connection()->getPdo()->exec($sql);
    }
}
