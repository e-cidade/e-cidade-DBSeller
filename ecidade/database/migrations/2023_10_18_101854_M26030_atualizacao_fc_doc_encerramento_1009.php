<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M26030AtualizacaoFcDocEncerramento1009 extends Migration
{
    /**
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
update contabilidade.conhistdocregra
set c92_regra = 'select * from fc_doc_encerramento_1009(fc_getsession(''DB_anousu'')::int, fc_getsession(''DB_instit'')::int) where valor > 0;'
where c92_anousu = 2023
  and c92_conhistdoc = 1009;

drop function if exists contabilidade.fc_doc_encerramento_1009;
drop type if exists tp_documento_1009 ;

create type tp_documento_1009 as
(
    reduzido_credito integer,
    reduzido_debito  numeric,
    codigo_recurso   integer,
    estrutural       varchar,
    valor            numeric,
    mensagem         text,
    erro             bool
);

create or replace function contabilidade.fc_doc_encerramento_1009(f_exercicio integer, f_instituicao integer) returns SETOF tp_documento_1009
    language plpgsql
as
$$
declare
    rtp_doc1009 tp_documento_1009%ROWTYPE;
    dataInicio  date;
    dataFim     date;
    creditar    int;
    debitar     int;
    rsDados     record;
begin

    dataInicio = (f_exercicio || '-01-01')::date;
    dataFim = (f_exercicio || '-12-31')::date;

    for rsDados in (
        with contas_encerramento as (
            select c47_debito as conta_encerramento,
                   c60_estrut
            from contabilidade.contrans
                 join contabilidade.contranslan on contrans.c45_seqtrans = contranslan.c46_seqtrans
                 join contabilidade.contranslr on contranslan.c46_seqtranslan = contranslr.c47_seqtranslan
                 join contabilidade.conplanoreduz on conplanoreduz.c61_reduz = contranslr.c47_debito
                      and conplanoreduz.c61_anousu = contranslr.c47_anousu
                 join contabilidade.conplano on conplano.c60_codcon = conplanoreduz.c61_codcon
                      and conplano.c60_anousu = conplanoreduz.c61_anousu
            where c45_coddoc = 1009
              and c45_anousu = f_exercicio
              and c45_instit = f_instituicao
              and c46_ordem = 1
            order by contranslr.c47_seqtranslr
        ), contas_encerrar as (
            select contas_encerramento.*,
                   bl.estrutural,
                   bl.reduzido,
                   bl.id_recurso,
                   bl.saldo_final,
                   bl.sinal_final
            from contas_encerramento, contabilidade.balancete_verificacao_por_recurso(
                    f_exercicio,
                    dataInicio,
                    dataFim,
                    false,
                    (select array_agg(c61_reduz)
                     from contabilidade.conplanoreduz
                          join contabilidade.conplano on conplanoreduz.c61_codcon = conplano.c60_codcon
                     where substr(c60_estrut, 1, 1) in ('3', '4')
                       and c61_instit = f_instituicao
                       and c61_anousu = f_exercicio
                    )::int[]
                  ) as bl
            where substring(bl.estrutural, 5, 1)::int = substring(contas_encerramento.c60_estrut, 5, 1)::int
        )
        select * from contas_encerrar
        where saldo_final != 0
        order by estrutural
    )
        loop

            creditar = rsDados.reduzido;
            debitar = rsDados.conta_encerramento;
            if rsDados.sinal_final = 'C' then
                debitar = rsDados.reduzido;
                creditar = rsDados.conta_encerramento;
            end if;
            rtp_doc1009.reduzido_credito = creditar;
            rtp_doc1009.reduzido_debito = debitar;

            rtp_doc1009.codigo_recurso = rsDados.id_recurso;
            rtp_doc1009.estrutural = rsDados.estrutural;
            rtp_doc1009.valor = abs(rsDados.saldo_final);

            return next rtp_doc1009;
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
        DB::connection()->getPdo()->exec(<<<SQL
update contabilidade.conhistdocregra
set c92_regra = 'select * from fc_doc_encerramento_2019(fc_getsession(''DB_anousu'')::int, fc_getsession(''DB_instit'')::int) where valor > 0;'
where c92_anousu = 2023
  and c92_conhistdoc = 1009;
SQL
        );
    }
}
