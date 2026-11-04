<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M23172AtualizacaoFundamentacaoLegal extends Migration
{
    private $codigoFundamentacao = 18;

    private function getCodigoFundamentacao()
    {
        $fundamentacao = DB::table('db_cadattdinamicoatributos')
            ->select('db109_sequencial')
            ->where('db109_nome', 'codigofundamentacao')
            ->first();

        if ($fundamentacao->db109_sequencial != 18) {
            $this->codigoFundamentacao = $fundamentacao->db109_sequencial;
        }
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->getCodigoFundamentacao();
        $this->inserirOpcoesFundamentacao();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->getCodigoFundamentacao();
        $this->excluirOpcoesFundamentacao();
    }

    private function inserirOpcoesFundamentacao()
    {
        DB::connection()->getPdo()->exec(<<<SQL
insert into db_cadattdinamicoatributosopcoes values (nextval('db_cadattdinamicoatributosopcoes_db18_sequencial_seq'), {$this->codigoFundamentacao}, '14284A34', 'Art. 34 da Lei no 14.284/21');
SQL
        );
    }

    private function excluirOpcoesFundamentacao()
    {
        DB::connection()->getPdo()->exec(<<<SQL
delete from db_cadattdinamicoatributosopcoes where db18_opcao = '14284A34' and db18_cadattdinamicoatributos = {$this->codigoFundamentacao};
SQL
        );
    }
}
