<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M26030FcEncerramento1023 extends Migration
{
    /**
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
drop function if exists fc_encerramento_1023()  cascade;
drop type if exists tp_documento_1023 cascade ;

create type tp_documento_1023 as
(
    conta_debito   integer,
    conta_credito  numeric,
    codigo_recurso integer,
    valor          numeric,
    saldo_conta    numeric,
    natureza_conta char
);

create or replace function fc_encerramento_1023() returns SETOF tp_documento_1023
    language plpgsql
as
$$
declare
    linha             tp_documento_1023%ROWTYPE;
    rsDados           record;
    anoSessao         integer;
    instituicaoSessao integer;
    dataInicio        date;
    dataFim           date;
begin

    anoSessao := fc_getsession('DB_anousu')::int;
    instituicaoSessao := fc_getsession('DB_instit')::int;
    dataInicio = (anoSessao || '-01-01')::date;
    dataFim = (anoSessao || '-12-31')::date;

    for rsDados in (

        with transacao as (
            select c47_debito  as conta_debito,
                   c47_credito as conta_credito,
                   case
                       when c60_naturezasaldo = 1 then 'D'
                       when c60_naturezasaldo = 2 then 'C'
                       end as natureza_cadastro_conta,
                   c45_anousu
            from contabilidade.contrans
                     join contabilidade.contranslan on contrans.c45_seqtrans = contranslan.c46_seqtrans
                     join contabilidade.contranslr on contranslan.c46_seqtranslan = contranslr.c47_seqtranslan
                     join contabilidade.conplanoreduz on conplanoreduz.c61_reduz = contranslr.c47_debito
                and conplanoreduz.c61_anousu = contranslr.c47_anousu
                     join contabilidade.conplano on conplano.c60_codcon = conplanoreduz.c61_codcon
                and conplano.c60_anousu = conplanoreduz.c61_anousu
            where c45_coddoc = 1023
              and c45_anousu = anoSessao
              and c45_instit = instituicaoSessao
              and c46_ordem = 1
            order by contranslr.c47_seqtranslr
        ), valores as (
            select
                conta_debito,
                conta_credito,
                natureza_cadastro_conta,
                id_recurso,
                saldo_anterior,
                saldo_debito,
                saldo_credito,
                saldo_final,
                sinal_final
            from transacao, contabilidade.balancete_verificacao_por_recurso(
                    2023,
                    dataInicio,
                    dataFim,
                    false,
                    array[conta_debito]::int[]
                    )
            where conta_debito = reduzido
        ) select * from valores
    )loop
            linha.saldo_conta = rsDados.saldo_anterior;
            linha.conta_debito = rsDados.conta_debito;
            linha.conta_credito = rsDados.conta_credito;
            linha.valor = abs(rsDados.saldo_final);
            linha.codigo_recurso = rsDados.id_recurso;

            linha.natureza_conta = 'C';
            if (linha.saldo_conta > 0) then
                linha.natureza_conta = 'D';
            end if;

            if (linha.natureza_conta <> rsDados.natureza_cadastro_conta) then
                linha.conta_debito = rsDados.conta_credito;
                linha.conta_credito = rsDados.conta_debito;
            end if;
            return next linha;

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
