<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M24099AjusteLancamentoDoc142 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $primeiroLancamento = <<<SQL
with primeiro_lancamento as (
  select
         c70_codlan,
         c70_anousu,
         reduz_cd.c61_codigo as recurso_debito,
         reduz_cc.c61_codigo as recurso_credito

   from conlancam
    join conlancamdoc on c71_codlan = c70_codlan
    join conhistdoc on c53_coddoc = c71_coddoc
    join conlancamval ON c69_codlan = c70_codlan and c69_ordem = 1
    join conplanoreduz reduz_cc ON reduz_cc.c61_reduz = c69_credito
         and reduz_cc.c61_anousu = c69_anousu
    join conplanoreduz reduz_cd ON reduz_cd.c61_reduz = c69_debito
         and reduz_cd.c61_anousu = c69_anousu
    where c70_anousu = 2023
      and c53_coddoc in (142)
)
SQL;
        $this->corrigeSegundoLancamento($primeiroLancamento);
        $this->corrigeTerceiroLancamento($primeiroLancamento);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }

    private function corrigeSegundoLancamento($primeiroLancamento)
    {
        $this->corrigeLancamento($primeiroLancamento, 'recurso_debito', 2);
    }

    private function corrigeTerceiroLancamento($primeiroLancamento)
    {
        $this->corrigeLancamento($primeiroLancamento, 'recurso_credito', 3);
    }

    private function corrigeLancamento($primeiroLancamento, $string, $ordem)
    {
        $table = "w_corrige_{$ordem}_lan";

        DB::connection()->getPdo()->exec(<<<SQL
drop table if exists $table;
create table $table as
$primeiroLancamento,  x_lancamento as (

  select c70_codlan,
         c70_anousu,
         $string as recurso_certo,
         c130_conta as conta,
         c130_natureza as natureza,
         c130_orctiporec as recurso_encontrado
    from primeiro_lancamento
    join conlancamval ON c69_codlan = c70_codlan and c69_ordem = $ordem

    join conlancamrecurso cr_cc on cr_cc.c130_conlancam = c70_codlan
       and cr_cc.c130_conta = conlancamval.c69_credito
       and cr_cc.c130_anousu = c69_anousu
       and cr_cc.c130_natureza = 'C'

  union all

   select c70_codlan,
         c70_anousu,
         $string as recurso_certo,
         c130_conta,
         c130_natureza,
         c130_orctiporec as recurso_encontrado
    from primeiro_lancamento
    join conlancamval ON c69_codlan = c70_codlan and c69_ordem = $ordem
    join conlancamrecurso cr_cd on cr_cd.c130_conlancam = c70_codlan
           and cr_cd.c130_conta = conlancamval.c69_credito
           and cr_cd.c130_anousu = c69_anousu
           and cr_cd.c130_natureza = 'D'
), diferenca_lancamento as (
  select * from x_lancamento where recurso_certo != recurso_encontrado
) select * from diferenca_lancamento;

update conlancamrecurso set c130_orctiporec = recurso_certo
from $table
where c130_conlancam = c70_codlan
  and c130_conta = conta
  and c130_anousu = c70_anousu
  and c130_natureza = natureza
SQL
        );
    }
}
