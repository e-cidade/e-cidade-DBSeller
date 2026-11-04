<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M26030FcEncerramentoDoc1019 extends Migration
{
    /**
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL

drop function if exists fc_encerramento_doc_1019();
drop type if exists tp_encerramento_despesa_1019;

create type tp_encerramento_despesa_1019 as
(
    codigo_transacao     integer,
    empenho              integer,
    dotacao              integer,
    conta_debito         integer,
    conta_credito        integer,
    codigo_recurso       integer,
    comparacao           integer,
    ano                  integer,
    natureza_saldo_conta varchar, /* natureza cadastrada na conta*/
    natureza_saldo       varchar, /* natureza processada para a conta*/
    valor                numeric,
    valor_debito         numeric,
    valor_credito        numeric,
    debito_menos_credito numeric,
    credito_menos_debito numeric,
    saldo_conta          numeric
);

create or replace function fc_encerramento_doc_1019() returns SETOF tp_encerramento_despesa_1019
    language plpgsql
as
$$
declare

    instituicaoSessao integer;
    anoSessao         integer;
    contaComparacao   integer;
    rTransacao        record;
    rDespesasEncerrar record;
    linha             tp_encerramento_despesa_1019%ROWTYPE;
    codigoTransacao   integer;

begin

    instituicaoSessao := fc_getsession('DB_instit');
    anoSessao := fc_getsession('DB_anousu');

    for rTransacao in
        select c47_seqtranslr,
               c47_debito,
               c47_credito,
               c47_compara,
               c47_debito,
               c47_credito,
               c46_ordem
        from contabilidade.contrans
                 join contabilidade.contranslan on c45_seqtrans = c46_seqtrans
                 join contabilidade.contranslr on c47_seqtranslan = c46_seqtranslan
        where c45_coddoc = 1019
          and c45_anousu = anoSessao
          and c45_instit = instituicaoSessao
        order by c47_seqtranslr
        loop

            contaComparacao := rTransacao.c47_debito;
            if rTransacao.c47_compara = 2 then
                contaComparacao := rTransacao.c47_credito;
            end if;

            for rDespesasEncerrar in
                select empenho,
                       dotacao,
                       ano,
                       natureza_saldo_conta,
                       id_recurso_credito,
                       sum(valor_credito)                                 as valor_credito,
                       sum(valor_debito)                                  as valor_debito,
                       (round(sum(valor_debito) - sum(valor_credito), 2)) as debito_menos_credito,
                       (round(sum(valor_credito) - sum(valor_debito), 2)) as credito_menos_debito
                from (
                         select c75_numemp         as empenho,
                                c73_coddot         as dotacao,
                                c69_anousu         as ano,
                                id_recurso_credito,
                                (select case
                                            when c60_naturezasaldo = 1 then 'D'
                                            when c60_naturezasaldo = 2 then 'C'
                                            end
                                 from contabilidade.conplanoreduz
                                          inner join contabilidade.conplano
                                                     on conplanoreduz.c61_codcon = conplano.c60_codcon
                                                         and conplanoreduz.c61_anousu = conplano.c60_anousu
                                 where c61_reduz = contaComparacao
                                   and c60_anousu = anoSessao
                                )                  as natureza_saldo_conta,
                                sum(valor_credito) as valor_credito,
                                sum(valor_debito)  as valor_debito
                         from (select coalesce(
                                          (case when c69_credito = contaComparacao then c69_valor end), 0
                                      ) as valor_credito,
                                      coalesce(
                                          (case when c69_debito = contaComparacao then c69_valor end), 0
                                      ) as valor_debito,
                                      c71_coddoc,
                                      c69_anousu,
                                      c75_numemp,
                                      c73_coddot,
                                      contabilidade.fc_recurso_conta_lancamento(c69_codlan, c69_credito, 'C') as id_recurso_credito
                               from contabilidade.conlancamval
                               inner join contabilidade.conlancamdoc on c69_codlan = c71_codlan
                               left join contabilidade.conlancamemp on c75_codlan = c69_codlan
                               left join contabilidade.conlancamdot on c73_codlan = c69_codlan
                               where (c69_debito = contaComparacao or c69_credito = contaComparacao)
                                 and c69_anousu = anoSessao
                              ) as x
                         group by 1, 2, 3, 4) as y
                group by dotacao, empenho, ano, natureza_saldo_conta, id_recurso_credito
                order by empenho, dotacao
                loop

                    if rDespesasEncerrar.credito_menos_debito = 0 then
                        continue;
                    end if;

                    linha.natureza_saldo_conta = rDespesasEncerrar.natureza_saldo_conta;
                    linha.conta_debito = rTransacao.c47_debito;
                    linha.conta_credito = rTransacao.c47_credito;
                    linha.saldo_conta = round(rDespesasEncerrar.valor_debito - rDespesasEncerrar.valor_credito, 2);
                    linha.codigo_transacao = rTransacao.c47_seqtranslr;
                    linha.valor = round(abs(linha.saldo_conta), 2);
                    linha.natureza_saldo = 'D';
                    linha.empenho = rDespesasEncerrar.empenho;
                    linha.dotacao = rDespesasEncerrar.dotacao;
                    linha.comparacao = rTransacao.c47_compara;
                    linha.ano = rDespesasEncerrar.ano;
                    linha.valor_debito = rDespesasEncerrar.valor_debito;
                    linha.valor_credito = rDespesasEncerrar.valor_credito;
                    linha.debito_menos_credito = rDespesasEncerrar.debito_menos_credito;
                    linha.credito_menos_debito = rDespesasEncerrar.credito_menos_debito;
                    linha.codigo_recurso = rDespesasEncerrar.id_recurso_credito;

                    if (linha.saldo_conta < 0) then
                        linha.natureza_saldo = 'C';
                    end if;

                    if (linha.natureza_saldo <> rDespesasEncerrar.natureza_saldo_conta) then

                        linha.conta_debito = rTransacao.c47_credito;
                        linha.conta_credito = rTransacao.c47_debito;
                    end if;

                    return next linha;
                end loop;
        end loop;
    return;
end;
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
        //
    }
}
