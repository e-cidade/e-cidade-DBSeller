<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M26030FcEncerramentoDoc1010 extends Migration
{
    /**
     * @return void
     */
    public function up()
    {
        // responsável pelo encerramento das contas dos documentos
        // 1010, 1020, 1021
        DB::connection()->getPdo()->exec(<<<SQL

drop function if exists fc_encerramento_doc_1010;
drop type if exists tp_encerramento_receita_1010;
create type tp_encerramento_receita_1010 as
(
    sequencial           integer,
    receita              integer,
    conta_debito         integer,
    conta_credito        integer,
    codigo_recurso       integer,
    ano                  integer,
    estrutural           varchar,
    natureza_saldo_conta varchar, /* natureza cadastrada na conta*/
    valor                numeric,
    valor_debito         numeric,
    valor_credito        numeric,
    natureza_saldo       varchar /* natureza processada para a conta*/,
    debito_menos_credito numeric,
    credito_menos_debito numeric,
    saldo_conta          numeric,
    compara              integer
);

create or replace function fc_encerramento_doc_1010(codigoDocumento integer) returns SETOF tp_encerramento_receita_1010
    language plpgsql
as
$$
declare

    instituicaoSessao integer;
    anoSessao         integer;
    contaComparacao   integer;
    rTransacao        record;
    rReceitasEncerrar record;
    linha             tp_encerramento_receita_1010%ROWTYPE;
    documento         integer;
begin

    instituicaoSessao := fc_getsession('DB_instit');
    anoSessao := fc_getsession('DB_anousu');
    documento := codigoDocumento;

    for rTransacao in
        select c47_seqtranslr,
               c47_debito,
               c47_credito,
               c47_compara,
               c46_ordem
        from contrans
                 join contranslan on c45_seqtrans = c46_seqtrans
                 join contranslr on c47_seqtranslan = c46_seqtranslan
        where c45_coddoc = documento
          and c45_anousu = anoSessao
          and c45_instit = instituicaoSessao
        order by c47_seqtranslr
        loop

            contaComparacao := rTransacao.c47_debito;
            if rTransacao.c47_compara = 2 then
                contaComparacao := rTransacao.c47_credito;
            end if;

            for rReceitasEncerrar in
                select receita,
                       ano,
                       estrutural,
                       id_recurso_credito,
                       (select case
                                   when c60_naturezasaldo = 1 then 'D'
                                   when c60_naturezasaldo = 2 then 'C'
                                   end
                        from contabilidade.conplanoreduz
                             join contabilidade.conplano on conplanoreduz.c61_codcon = conplano.c60_codcon
                                  and conplanoreduz.c61_anousu = conplano.c60_anousu
                        where c61_reduz = contaComparacao
                          and c60_anousu = anoSessao
                       )                                                  as natureza_saldo_conta,
                       sum(valor_credito)                                 as valor_credito,
                       sum(valor_debito)                                  as valor_debito,
                       (round(sum(valor_debito) - sum(valor_credito), 2)) as debito_menos_credito,
                       (round(sum(valor_credito) - sum(valor_debito), 2)) as credito_menos_debito
                from (
                         select o70_codrec         as receita,
                                c69_anousu         as ano,
                                o57_fonte          as estrutural,
                                id_recurso_credito,
                                sum(valor_credito) as valor_credito,
                                sum(valor_debito)  as valor_debito
                         from (select o70_codrec,
                                      coalesce(
                                        (case when c69_credito = contaComparacao then c69_valor end), 0
                                      ) as valor_credito,
                                      coalesce(
                                        (case when c69_debito = contaComparacao then c69_valor end), 0
                                      ) as valor_debito,
                                      c53_tipo,
                                      c71_coddoc,
                                      c69_anousu,
                                      o57_fonte,
                                      contabilidade.fc_recurso_conta_lancamento(c69_codlan, c69_credito, 'C') as id_recurso_credito
                               from contabilidade.conlancam
                                    join contabilidade.conlancamval
                                         on conlancam.c70_codlan = conlancamval.c69_codlan
                                    join contabilidade.conlancamdoc
                                         on conlancam.c70_codlan = conlancamdoc.c71_codlan
                                    join contabilidade.conhistdoc
                                         on conlancamdoc.c71_coddoc = conhistdoc.c53_coddoc
                                    join contabilidade.conlancaminstit
                                         on conlancam.c70_codlan = conlancaminstit.c02_codlan
                                    left join contabilidade.conlancamrec
                                         on conlancamrec.c74_codlan = conlancam.c70_codlan
                                    left join orcamento.orcreceita on o70_codrec = c74_codrec
                                         and o70_anousu = c69_anousu
                                    left join orcamento.orcfontes on orcfontes.o57_codfon = orcreceita.o70_codfon
                                         and orcfontes.o57_anousu = orcreceita.o70_anousu
                               where c69_anousu = anoSessao
                                 and (c69_credito = contaComparacao or c69_debito = contaComparacao)
                                 and c02_instit = instituicaoSessao
                              ) as x
                         group by 1, 2, 3, 4) as y
                group by receita, ano, estrutural, 4
                loop
                    linha.saldo_conta = round(rReceitasEncerrar.valor_debito - rReceitasEncerrar.valor_credito, 2);
                    linha.valor = round(abs(linha.saldo_conta), 2);
                    linha.sequencial = rTransacao.c47_seqtranslr;
                    linha.receita = rReceitasEncerrar.receita;
                    linha.ano = rReceitasEncerrar.ano;
                    linha.estrutural = rReceitasEncerrar.estrutural;
                    linha.valor_debito = rReceitasEncerrar.valor_debito;
                    linha.valor_credito = rReceitasEncerrar.valor_credito;
                    linha.debito_menos_credito = rReceitasEncerrar.debito_menos_credito;
                    linha.credito_menos_debito = rReceitasEncerrar.credito_menos_debito;
                    linha.compara = rTransacao.c47_compara;

                    linha.natureza_saldo_conta = rReceitasEncerrar.natureza_saldo_conta;
                    linha.natureza_saldo = 'D';
                    if (linha.saldo_conta < 0) then
                        linha.natureza_saldo = 'C';
                    end if;

                    linha.codigo_recurso = rReceitasEncerrar.id_recurso_credito;
                    linha.conta_debito = rTransacao.c47_debito;
                    linha.conta_credito = rTransacao.c47_credito;
                    if (linha.natureza_saldo <> rReceitasEncerrar.natureza_saldo_conta) then
                        linha.conta_debito = rTransacao.c47_credito;
                        linha.conta_credito = rTransacao.c47_debito;
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
        //
    }
}
