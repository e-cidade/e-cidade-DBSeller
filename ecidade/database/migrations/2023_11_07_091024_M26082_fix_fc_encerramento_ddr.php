<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M26082FixFcEncerramentoDdr extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL

create or replace function fc_encerramento_ddr() returns SETOF tp_encerramento_ddr
    language plpgsql
as
$$
declare

    instituicaoSessao integer;
    anoSessao         integer;
    usaApropriacaoRetencao boolean default false;
    rRecursoEncerrar  record;
    resourceContas  record;
    linha             tp_encerramento_ddr%ROWTYPE;
    contaDebitoLancamento integer;
    contaCreditoLancamento integer;
    valorCalculado numeric;

begin

    instituicaoSessao := fc_getsession('DB_instit');
    anoSessao := fc_getsession('DB_anousu');
    usaApropriacaoRetencao := fc_getsession('DB_usa_apropriacao_retencao') = 't';

    for resourceContas in (
        select distinct c47_debito, (case when c60_naturezasaldo = 1 then 'D' else 'C' end) as natureza
        from contrans
         join contranslan on c45_seqtrans = c46_seqtrans
         join contranslr on c47_seqtranslan = c46_seqtranslan
         join conplanoreduz on c61_reduz = c47_debito
            and c61_anousu = anoSessao
         join conplano on c60_codcon = c61_codcon
            and c60_anousu = anoSessao
        where c45_coddoc = 1022
          and c45_anousu = anoSessao
          and c45_instit = instituicaoSessao
    ) loop
            for rRecursoEncerrar in (

                select tipo_recurso,
                       codigo_recurso,
                       (valor_debito + coalesce(
                               (select c62_vlrdeb
                                  from conplanoexe
                                 where c62_reduz = resourceContas.c47_debito
                                   and c62_anousu = anoSessao
                                   and c62_codrec = codigo_recurso), 0)
                       ) as valor_debito,

                       (valor_credito +  coalesce(
                               (select c62_vlrcre
                                  from conplanoexe
                                 where c62_reduz = resourceContas.c47_debito
                                   and c62_anousu = anoSessao
                                   and c62_codrec = codigo_recurso), 0)
                       )as valor_credito
                  from (select 1 as tipo_recurso,
                               case
                                   when conlancamval.c69_debito = resourceContas.c47_debito
                                       then contabilidade.fc_recurso_conta_lancamento(c69_codlan, c69_debito, 'D')
                                   when conlancamval.c69_credito = resourceContas.c47_debito
                                       then contabilidade.fc_recurso_conta_lancamento(c69_codlan, c69_credito, 'C')
                               end as codigo_recurso,
                               round(sum(case
                                             when conlancamval.c69_debito = resourceContas.c47_debito then c70_valor
                                             else 0 end), 2
                               ) as valor_debito,
                               round(sum(case
                                             when conlancamval.c69_credito = resourceContas.c47_debito then c70_valor
                                             else 0 end) , 2
                               ) as valor_credito

                        from conlancam
                                 inner join conlancamval on conlancamval.c69_codlan = conlancam.c70_codlan
                                 inner join conlancamdoc on conlancamdoc.c71_codlan = conlancam.c70_codlan
                                 inner join conhistdoc on c53_coddoc = c71_coddoc
                                 left join conlancamemp on conlancamemp.c75_codlan = conlancam.c70_codlan
                                 left join empempenho on empempenho.e60_numemp = conlancamemp.c75_numemp
                                 left join orcdotacao on orcdotacao.o58_coddot = empempenho.e60_coddot
                            and orcdotacao.o58_anousu = empempenho.e60_anousu
                                 left join orctiporec on orctiporec.o15_codigo = orcdotacao.o58_codigo
                        where conlancam.c70_data between cast(anoSessao || '-01-01' as date) and cast(anoSessao || '-12-31' as date)
                          and (conlancamval.c69_credito = resourceContas.c47_debito or
                               conlancamval.c69_debito = resourceContas.c47_debito)
                          and ((c53_tipo in (30, 31) and orctiporec.o15_tipo = 1) or (c53_coddoc in (3000)))
                          and c71_coddoc not in (6002, 6003, 6008, 6009, 6010, 6011)
                        group by 1, 2
                  ) as x
                union

                select o15_tipo as tipo_recurso,
                       case
                           when conlancamval.c69_debito = resourceContas.c47_debito
                               then contabilidade.fc_recurso_conta_lancamento(c69_codlan, c69_debito, 'D')
                           when conlancamval.c69_credito = resourceContas.c47_debito
                               then contabilidade.fc_recurso_conta_lancamento(c69_codlan, c69_credito, 'C')
                       end as codigo_recurso,
                       round(sum(case when conlancamval.c69_debito = resourceContas.c47_debito then c70_valor else 0 end),2) as valor_debito,
                       round(sum(case when conlancamval.c69_credito = resourceContas.c47_debito then c70_valor else 0 end),2) as valor_credito
                from conlancam
                inner join conlancamval on conlancamval.c69_codlan = conlancam.c70_codlan
                inner join conlancamdoc on conlancamdoc.c71_codlan = conlancam.c70_codlan
                inner join conlancamemp on conlancamemp.c75_codlan = conlancam.c70_codlan
                inner join empempenho   on empempenho.e60_numemp = conlancamemp.c75_numemp
                inner join orcdotacao   on orcdotacao.o58_coddot = empempenho.e60_coddot
                           and orcdotacao.o58_anousu = empempenho.e60_anousu
                inner join orctiporec on orctiporec.o15_codigo = orcdotacao.o58_codigo
                inner join conhistdoc on c53_coddoc = c71_coddoc
                where conlancam.c70_data between cast(anoSessao || '-01-01' as date) and cast(anoSessao || '-12-31' as date)
                  and (conlancamval.c69_credito = resourceContas.c47_debito or conlancamval.c69_debito = resourceContas.c47_debito)
                  and c53_tipo in (30, 31) and c71_coddoc not in (6002, 6003, 6008, 6009, 6010, 6011)
                  and orctiporec.o15_tipo = 2
                group by 1,2

                union

                select 3 as tipo_recurso,
                       case
                           when conlancamval.c69_debito = resourceContas.c47_debito
                               then contabilidade.fc_recurso_conta_lancamento(c69_codlan, c69_debito, 'D')
                           when conlancamval.c69_credito = resourceContas.c47_debito
                               then contabilidade.fc_recurso_conta_lancamento(c69_codlan, c69_credito, 'C')
                       end as codigo_recurso,
                       round(sum(case when conlancamval.c69_debito = resourceContas.c47_debito then c70_valor else 0 end),2) as valor_debito,
                       round(sum(case when conlancamval.c69_credito = resourceContas.c47_debito then c70_valor else 0 end),2) as valor_credito
                from conlancam
                     inner join conlancamval on conlancamval.c69_codlan = conlancam.c70_codlan
                     inner join conlancamdoc on conlancamdoc.c71_codlan = conlancam.c70_codlan
                     left join conlancamemp on conlancamemp.c75_codlan = conlancam.c70_codlan
                where conlancam.c70_data between cast(anoSessao || '-01-01' as date) and cast(anoSessao || '-12-31' as date)
                  and (conlancamval.c69_credito = resourceContas.c47_debito or conlancamval.c69_debito = resourceContas.c47_debito)
                  and c71_coddoc in (120, 121, 161, 163, 151, 153, 6002, 6003, 6008, 6009, 6010, 6011)
                group by 1,2
            ) loop

                    select c47_debito, c47_credito
                    into contaDebitoLancamento, contaCreditoLancamento
                    from contrans
                    join contranslan on c45_seqtrans = c46_seqtrans
                    join contranslr on c47_seqtranslan = c46_seqtranslan
                    where c45_coddoc = 1022
                      and c45_anousu = anoSessao
                      and c45_instit = instituicaoSessao

                      and c47_ref = rRecursoEncerrar.tipo_recurso
                      and c47_compara = 17;

                    valorCalculado = round(rRecursoEncerrar.valor_credito - rRecursoEncerrar.valor_debito, 2);
                    linha.valor = abs(valorCalculado);
                    linha.natureza_saldo = 'C';
                    linha.conta_debito = contaDebitoLancamento;
                    linha.conta_credito = contaCreditoLancamento;
                    linha.codigo_recurso = rRecursoEncerrar.codigo_recurso;
                    linha.compara_tipo_recurso = rRecursoEncerrar.tipo_recurso;

                    if (valorCalculado < 0) then
                        linha.natureza_saldo = 'D';
                    end if;

                    if (linha.natureza_saldo <> resourceContas.natureza) then
                        linha.conta_debito = contaCreditoLancamento;
                        linha.conta_credito = contaDebitoLancamento;
                    end if;

                    return next linha;

                end loop;
        end loop;
    return;
end ;
$$;
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
    }
}
