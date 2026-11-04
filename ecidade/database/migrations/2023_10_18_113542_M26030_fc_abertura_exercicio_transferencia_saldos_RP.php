<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M26030FcAberturaExercicioTransferenciaSaldosRP extends Migration
{
    /**
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
drop function if exists fc_abertura_exercicio_transferencia_saldos_RP;
drop type if exists tp_abertura_exercicio_transferencia_saldos_rp;
create type tp_abertura_exercicio_transferencia_saldos_rp as
(
    empenho        integer,
    codigo_recurso integer,
    valor          numeric,
    ano            integer,
    ano_empenho    integer,
    desdobramento  integer,
    credor         integer
);

create or replace function fc_abertura_exercicio_transferencia_saldos_RP(tipo integer) returns SETOF tp_abertura_exercicio_transferencia_saldos_rp
    language plpgsql
as
$$
declare

    iAnoUsu            integer;
    iInstit            integer;
    rValoresLancamento record;
    campo              text;
    sql                text;
    rtp_valores        tp_abertura_exercicio_transferencia_saldos_rp%ROWTYPE;

begin

    iAnoUsu := cast((select fc_getsession('DB_anousu')) as integer);
    iInstit := cast((select fc_getsession('DB_instit')) as integer);

    if iAnoUsu is null then
        raise exception 'ERRO : Variavel de sessao [DB_anousu] nao encontrada.';
    end if;

    if iInstit is null then
        raise exception 'ERRO : Variavel de sessao [DB_instit] nao encontrada.';
    end if;

    campo := 'e91_vlremp - e91_vlrliq - e91_vlranu';
    if tipo = 2 then
        campo := 'e91_vlrliq - e91_vlrpag';
    end if;
    sql := 'select distinct e91_anousu as ano,
                   e60_anousu as ano_empenho,
                   e60_numemp as empenho,
                   e60_numcgm as credor,
                   e64_codele as desdobramento,
                   case when o206_recurso is null then o58_codigo else o206_recurso end id_recurso,
                   round(' || campo || ', 2)::numeric as valor
           from empenho.empresto
                join empenho.empempenho on e60_numemp = e91_numemp
                join orcdotacao on o58_anousu = e60_anousu
                     and o58_coddot = e60_coddot

                join empenho.empelemento on empempenho.e60_numemp = empelemento.e64_numemp
                left join orcamento.origemcomplementorecurso on o206_numero = empempenho.e60_numemp
                     and o206_origem = 10
           where e91_anousu = ' || iAnoUsu || '
             and e60_anousu = ' || iAnoUsu - 1 || '
             and e60_instit = ' || iInstit || '
             and round(' || campo || ', 2) > 0 order by e60_numemp';

    for rValoresLancamento in execute sql
        loop

            rtp_valores.valor := rValoresLancamento.valor;
            rtp_valores.ano_empenho = rValoresLancamento.ano_empenho;
            rtp_valores.empenho = rValoresLancamento.empenho;
            rtp_valores.codigo_recurso = rValoresLancamento.id_recurso;
            rtp_valores.ano = rValoresLancamento.ano;
            rtp_valores.desdobramento = rValoresLancamento.desdobramento;
            rtp_valores.credor = rValoresLancamento.credor;
            return next rtp_valores;

        end loop;
    return;
end
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
