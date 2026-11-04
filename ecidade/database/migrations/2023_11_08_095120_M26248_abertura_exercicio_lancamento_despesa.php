<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M26248AberturaExercicioLancamentoDespesa extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL

drop function if exists fc_abertura_exercicio_lancamento_despesa();
drop type if exists tp_abertura_exercicio_dotacao;

create type tp_abertura_exercicio_dotacao as
(
    dotacao integer,
    valor   numeric,
    ano     numeric,
    codigo_recurso integer
);

create function fc_abertura_exercicio_lancamento_despesa() returns setof tp_abertura_exercicio_dotacao as
$$
declare


    iAnoUsu              integer;
    iInstit              integer;
    rValoresLancamento   record;
    rtp_valores_abertura tp_abertura_exercicio_dotacao%ROWTYPE;

begin
    iAnoUsu := cast((select fc_getsession('DB_anousu')) as integer);
    iInstit := cast((select fc_getsession('DB_instit')) as integer);

    if iAnoUsu is null then
        raise exception 'ERRO : Variavel de sessao [DB_anousu] nao encontrada.';
    end if;

    if iInstit is null then
        raise exception 'ERRO : Variavel de sessao [DB_instit] nao encontrada.';
    end if;


    for rValoresLancamento in
        select o58_coddot as dotacao,
               o58_valor  as valor,
               o58_anousu  as ano,
               o58_codigo
          from orcamento.orcdotacao
         where o58_anousu = iAnoUsu
           and o58_instit = iInstit
           and o58_valor <> 0
        loop
            rtp_valores_abertura.valor = rValoresLancamento.valor;
            rtp_valores_abertura.dotacao = rValoresLancamento.dotacao;
            rtp_valores_abertura.ano = rValoresLancamento.ano;
            rtp_valores_abertura.codigo_recurso = rValoresLancamento.o58_codigo;

            return next rtp_valores_abertura;
        end loop;
    return;
end
$$
language 'plpgsql';
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
