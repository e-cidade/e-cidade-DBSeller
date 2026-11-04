<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M26030AtualizacaoFcValoresEncerramentoEmpenhoRp extends Migration
{
    /**
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
drop function fc_valores_encerramento_empenho_rp(varchar, boolean);
drop type tp_valores_encerramento;
create type tp_valores_encerramento as (
    empenho                  integer,
    anousu                   integer,
    codigo_recurso           integer,
    valor_credito            numeric,
    valor_debito             numeric,
    valor_a_liquidar_empenho numeric,
    valor                    numeric
);

create or replace function fc_valores_encerramento_empenho_rp(varchar, boolean) returns setof tp_valores_encerramento as
$$
declare

    conta alias for $1;
    lResto alias for $2;
    contasProcessar          text;
    iAnoUsu                  integer;
    iInstit                  integer;
    iDocManual               integer;
    sWhereEmpenho            text default '';
    sJoinEmpenho             text default '';
    tSql                     text default '';
    rValoresLancamento       record;
    rtp_valores_encerramento tp_valores_encerramento%ROWTYPE;

begin

    iDocManual := 3000;

    iAnoUsu := cast((select fc_getsession('DB_anousu')) as integer);
    iInstit := cast((select fc_getsession('DB_instit')) as integer);

    if iAnoUsu is null then
        raise exception 'ERRO : Variavel de sessao [DB_anousu] nao encontrada.';
    end if;

    if iInstit is null then
        raise exception 'ERRO : Variavel de sessao [DB_instit] nao encontrada.';
    end if;

    select  array_to_string(array_accum(c61_reduz), ',')
    into contasProcessar
    from (select c61_reduz,
                 c61_codcon,
                 c60_estrut,
                 c61_anousu,
                 (select saldo_final
                  from fc_planosaldonovo_record(
                    c61_anousu,
                    c61_reduz,
                    cast( iAnousu::text||'-01-01' as date),
                    cast( iAnousu::text||'-12-31' as date),
                    true
                       )
                 ) as saldo_final
          from conplano
                   inner join conplanoreduz on c61_codcon = c60_codcon and c61_anousu = c60_anousu
          where c60_estrut like conta||'%'
            and c61_anousu = iAnoUsu
            and c61_instit = iInstit) as saldos
    where saldo_final > 0;
    if contasProcessar = '' then
        return ;
    end if;

    sWhereEmpenho = ' (1 = 1)';
    if lResto then
        sWhereEmpenho = ' e91_anousu = ' || iAnoUsu;
        sJoinEmpenho = ' inner join empenho.empresto   on e60_numemp = e91_numemp ';
    end if;

    rtp_valores_encerramento.empenho := 0;
    rtp_valores_encerramento.anousu := 0;
    rtp_valores_encerramento.codigo_recurso := 0;
    rtp_valores_encerramento.valor_credito := 0;
    rtp_valores_encerramento.valor_debito := 0;
    rtp_valores_encerramento.valor_a_liquidar_empenho := 0;
    rtp_valores_encerramento.valor := 0;

    tSql := 'select empenho             as empenho,
                e60_anousu              as anousu,
                id_recurso,
                sum(valor_credito)                                 as valor_credito,
                sum(valor_debito)                                  as valor_debito,
                sum(valor_a_liquidar_empenho)                      as valor_a_liquidar_empenho,
                (round(sum(valor_credito) - sum(valor_debito), 2)) as valor
         from (select c75_numemp         as empenho,
                      e60_anousu,
                      id_recurso,
                      sum(valor_credito) as valor_credito,
                      sum(valor_debito)  as valor_debito,
                      (select round(e60_vlremp - e60_vlranu - e60_vlrliq, 2) from empenho.empempenho where e60_numemp = c75_numemp) as valor_a_liquidar_empenho
               from (select c75_numemp,
                            fc_recurso_conta_lancamento(c69_codlan, c69_credito, ''C'') as id_recurso,
                            coalesce((case when c69_credito = reduzido_novo then c69_valor end), 0) as valor_credito,
                            coalesce((case when c69_debito = reduzido_novo then c69_valor end), 0) as valor_debito,
                            c53_tipo,
                            c71_coddoc,
                            e60_anousu
                     from (select c61_reduz as reduzido_novo
                           from contabilidade.conplanoreduz
                                    inner join contabilidade.conplano
                                               on conplanoreduz.c61_codcon = conplano.c60_codcon and
                                                  conplanoreduz.c61_anousu = conplano.c60_anousu
                           where c61_reduz in (' || contasProcessar ||' )
                             and c61_anousu = ' || iAnoUsu || '
                             and c61_instit = ' || iInstit || '
                          ) as mao,
                          contabilidade.conlancamemp
                              inner join contabilidade.conlancam on c70_codlan = conlancamemp.c75_codlan
                              inner join contabilidade.conlancamval on conlancam.c70_codlan = conlancamval.c69_codlan
                              inner join contabilidade.conlancamdoc on conlancam.c70_codlan = conlancamdoc.c71_codlan
                              inner join contabilidade.conhistdoc on conlancamdoc.c71_coddoc = conhistdoc.c53_coddoc
                              inner join empenho.empempenho on c75_numemp = e60_numemp
                              ' || sJoinEmpenho || '
                              inner join contabilidade.conlancaminstit on conlancam.c70_codlan = conlancaminstit.c02_codlan
                     where c69_anousu   = ' || iAnoUsu || '
                       and (c69_credito = reduzido_novo or c69_debito = reduzido_novo)
                       and ' || sWhereEmpenho || '
                       and c02_instit   = ' || iInstit || '
                    ) as x
               group by c75_numemp, e60_anousu, id_recurso
               union all
               select 0                 as empenho,
                      ' || iAnoUsu || ' as anousu,
                      id_recurso,
                      sum(valor_credito) as valor_credito,
                      sum(valor_debito)  as valor_debito,
                      0
               from (select fc_recurso_conta_lancamento(c69_codlan, c69_credito, ''C'') as id_recurso,
                            coalesce((case when c69_credito = reduzido_novo then c69_valor end), 0) as valor_credito,
                            coalesce((case when c69_debito = reduzido_novo then c69_valor end), 0) as valor_debito,
                            c53_tipo,
                            c71_coddoc
                     from (select c61_reduz as reduzido_novo
                           from contabilidade.conplanoreduz
                                    inner join contabilidade.conplano on conplanoreduz.c61_codcon = conplano.c60_codcon
                                                                     and conplanoreduz.c61_anousu = conplano.c60_anousu
                         where c61_reduz in (' || contasProcessar ||' )
                             and c61_anousu = ' || iAnoUsu || '
                             and c61_instit = ' || iInstit || ' ) as mao,
                          contabilidade.conlancam
                              inner join contabilidade.conlancamval    on conlancam.c70_codlan = conlancamval.c69_codlan
                              inner join contabilidade.conlancamdoc    on conlancam.c70_codlan = conlancamdoc.c71_codlan
                              inner join contabilidade.conhistdoc      on conlancamdoc.c71_coddoc = conhistdoc.c53_coddoc
                              inner join contabilidade.conlancaminstit on conlancam.c70_codlan = conlancaminstit.c02_codlan
                     where c69_anousu   = ' || iAnoUsu || '
                       and (c69_credito = reduzido_novo or c69_debito = reduzido_novo)
                       and c53_tipo     = ' || iDocManual || '
                       and c02_instit   = ' || iInstit || '
                    ) as x
               group by c71_coddoc, id_recurso ) as t
         group by empenho, e60_anousu, id_recurso
        having (round(sum(valor_credito) - sum(valor_debito), 2) <> 0)
        order by empenho ';

    for rValoresLancamento in execute tSql
        loop

            rtp_valores_encerramento.empenho := rValoresLancamento.empenho;
            rtp_valores_encerramento.anousu := rValoresLancamento.anousu;
            rtp_valores_encerramento.codigo_recurso := rValoresLancamento.id_recurso;
            rtp_valores_encerramento.valor_credito := rValoresLancamento.valor_credito;
            rtp_valores_encerramento.valor_debito := rValoresLancamento.valor_debito;
            rtp_valores_encerramento.valor_a_liquidar_empenho := rValoresLancamento.valor_a_liquidar_empenho;
            rtp_valores_encerramento.valor := rValoresLancamento.valor;

            return next rtp_valores_encerramento;

        end loop;
    return;
end;
$$ language 'plpgsql';
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
