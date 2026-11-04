<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M27330NovasLeisLicitacoesFundamentacaoLegal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->getCodigoFundamentacao();
        $this->inserirNovasOpcoes();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->getCodigoFundamentacao();
        $this->excluirNovasOpcoes();
    }

    private $codigoFundamentacao = 18;

    private function getCodigoFundamentacao()
    {
        $fundamentacaoCodigo = DB::table('db_cadattdinamicoatributos')
            ->select('db109_sequencial')
            ->where('db109_nome', 'codigofundamentacao')
            ->first();

        $this->codigoFundamentacao = $fundamentacaoCodigo->db109_sequencial;
    }

    private function inserirNovasOpcoes()
    {
        DB::connection()->getPdo()->exec(<<<SQL
insert into db_cadattdinamicoatributosopcoes values (nextval('db_cadattdinamicoatributosopcoes_db18_sequencial_seq'), {$this->codigoFundamentacao}, '13303A31P4', 'Art. 31, § 4º, da Lei 13.303/2016');
insert into db_cadattdinamicoatributosopcoes values (nextval('db_cadattdinamicoatributosopcoes_db18_sequencial_seq'), {$this->codigoFundamentacao}, '13303A63III', 'Art. 63, inc. III, da Lei 13.303/2016');
SQL
        );
    }

    private function excluirNovasOpcoes()
    {
        DB::connection()->getPdo()->exec(<<<SQL
delete from db_cadattdinamicoatributosopcoes where db18_opcao in (
    '13303A31P4',
    '13303A63III'
) and db18_cadattdinamicoatributos = {$this->codigoFundamentacao};
SQL
        );
    }
}
