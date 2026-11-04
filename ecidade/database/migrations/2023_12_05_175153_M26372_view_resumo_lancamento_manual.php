<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M26372ViewResumoLancamentoManual extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
drop function if exists contabilidade.resumo_reduzido cascade;
drop view if exists contabilidade.resumo_consulta_lancamento_manual cascade;

create function contabilidade.resumo_reduzido(reduzido integer, exercicio integer) returns jsonb
    language plpgsql
as
$$
declare
    retorno jsonb;
begin
    select ('{"codcon" : ' || c60_codcon
                || ', "codigo": ' || c60_codigo
                || ', "reduzido" :' || c61_reduz
                || ', "estrutural": "'|| c60_estrut
                ||'", "descricao": "'|| c60_descr
                ||'" }')::jsonb as retorno
        into retorno
        from contabilidade.conplanoreduz
                 join contabilidade.conplano on conplanoreduz.c61_codcon = conplano.c60_codcon
            and conplanoreduz.c61_anousu = conplano.c60_anousu
        where c61_reduz = reduzido
          and c61_anousu = exercicio;

    return retorno;
end ;
$$;

create view contabilidade.resumo_consulta_lancamento_manual as
select c70_codlan,
       c70_anousu,
       c70_valor,
       c70_data,
       c02_instit,
       c160_codigo,
       c160_lote,
       c53_coddoc,
       c53_descr,
       c53_tipo,
       c69_codhist,
       c50_descr,
       contabilidade.fc_recurso_conta_lancamento(c70_codlan, c69_credito, 'C') as recurso_credito,
       contabilidade.fc_recurso_conta_lancamento(c70_codlan, c69_debito, 'D') as recurso_debito,
       contabilidade.resumo_reduzido(c69_credito, c69_anousu) as credito,
       contabilidade.resumo_reduzido(c69_debito, c69_anousu)  as debito,
       c73_coddot,
       c76_numcgm,
       c75_numemp,
       c74_codrec,
       c72_complem,
       exists(select 1 from conlancamretificacao where c135_codlaninclusao = c70_codlan) as tem_extorno
from contabilidade.conlancam
     join contabilidade.conlancamval on conlancamval.c69_codlan = conlancam.c70_codlan
     join contabilidade.conlancaminstit on conlancaminstit.c02_codlan = conlancam.c70_codlan
     join contabilidade.conlancamdoc on conlancamdoc.c71_codlan = conlancam.c70_codlan
     join contabilidade.conhistdoc on conhistdoc.c53_coddoc = conlancamdoc.c71_coddoc
     join contabilidade.conhist on conhist.c50_codhist = conlancamval.c69_codhist
     join contabilidade.lotelancamentoconlancam on lotelancamentoconlancam.c161_conlancam = conlancamval.c69_codlan
     join contabilidade.lotelancamentos lote on  lote.c160_codigo = lotelancamentoconlancam.c161_lotelancamento
     left join contabilidade.conlancamcompl on conlancamcompl.c72_codlan = conlancam.c70_codlan
     left join contabilidade.conlancamemp on conlancamemp.c75_codlan = conlancam.c70_codlan
     left join contabilidade.conlancamcgm on conlancamcgm.c76_codlan = conlancam.c70_codlan
     left join contabilidade.conlancamdot on conlancamdot.c73_codlan = conlancam.c70_codlan
     left join contabilidade.conlancamrec on conlancamrec.c74_codlan = conlancam.c70_codlan
where c53_tipo in (3000,3001);

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
        DB::connection()->getPdo()->exec(<<<SQL
drop view if exists contabilidade.resumo_consulta_lancamento_manual cascade;
drop function if exists contabilidade.resumo_reduzido cascade;
SQL
        );
    }
}
