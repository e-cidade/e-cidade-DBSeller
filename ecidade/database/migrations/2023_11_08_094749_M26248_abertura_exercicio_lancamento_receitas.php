<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M26248AberturaExercicioLancamentoReceitas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
drop function if exists fc_abertura_exercicio_lancamento_receitas();
drop type if exists tp_abertura_exercicio_receita;

create type tp_abertura_exercicio_receita as
(
    receita integer,
    valor   numeric,
    codigo_recurso integer
);

create function fc_abertura_exercicio_lancamento_receitas() returns setof tp_abertura_exercicio_receita as
$$
declare
    iAnoUsu              integer;
    iInstit              integer;
    rValoresLancamento   record;
    rtp_valores_abertura tp_abertura_exercicio_receita%ROWTYPE;
begin
    iAnoUsu := cast((select fc_getsession('DB_anousu')) as integer);
    iInstit := cast((select fc_getsession('DB_instit')) as integer);

    if iAnoUsu is null then
        raise exception 'ERRO : Variavel de sessao [DB_anousu] nao encontrada.';
    end if;

    if iInstit is null then
        raise exception 'ERRO : Variavel de sessao [DB_instit] nao encontrada.';
    end if;


    for rValoresLancamento in(
        select
              o70_codrec as receita,
              o70_codigo,
              o70_valor as valor
          from orcamento.orcreceita
          join orcamento.orcfontes on orcfontes.o57_codfon = orcreceita.o70_codfon
               and orcfontes.o57_anousu = orcreceita.o70_anousu
          where o70_anousu = iAnoUsu
            and o70_instit = iInstit
    ) loop
            rtp_valores_abertura.valor = rValoresLancamento.valor;
            rtp_valores_abertura.receita = rValoresLancamento.receita;
            rtp_valores_abertura.codigo_recurso = rValoresLancamento.o70_codigo;

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
