<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M26248ResumoSaldoEmpenho extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
drop view if exists contabilidade.v_resumo_saldo_empenho;

create view contabilidade.v_resumo_saldo_empenho as
select
    e60_numemp,
    e60_anousu,
    e60_instit,
    c70_data,
    sum(case when c53_tipo = 10 then c70_valor else 0 end) as valor_empenhado,
    sum(case when c53_tipo = 11 then c70_valor else 0 end) as valor_anulado,
    sum(case when c53_tipo = 20 then c70_valor
             when c53_tipo = 21 then c70_valor -1
             when c53_tipo = 11 and c71_coddoc = 31 then c70_valor -1
             else 0
        end) as valor_liquidado,
    sum(case when c53_tipo = 30 then c70_valor
             when c53_tipo = 31 then c70_valor -1
             else 0
        end) as valor_pago
from empempenho
join conlancamemp on c75_numemp = e60_numemp
join conlancam on c70_codlan = c75_codlan
join conlancamdoc on c71_codlan = c70_codlan
join conhistdoc on c71_coddoc = c53_coddoc
group by e60_numemp,
         e60_anousu,
         e60_instit,
         c70_data;
SQL
        );
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
}
