<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M26248Doc2032Abertura extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
drop function if exists doc_2032_abertura;
drop type if exists tp_abertura_rps cascade;

create type tp_abertura_rps as
(
    empenho             integer,
    valor               numeric,
    codigo_recurso      integer
);

create function doc_2032_abertura() returns setof tp_abertura_rps
    language plpgsql
as
$$
declare
    rDados record;
    retorno  tp_abertura_rps%ROWTYPE;
    anousu integer;
    instituicao integer;
begin
    anousu := cast((select fc_getsession('DB_anousu')) as integer);
    instituicao := cast((select fc_getsession('DB_instit')) as integer);

    if anousu is null then
        raise exception 'ERRO : Variavel de sessao [DB_anousu] nao encontrada.';
    end if;

    if instituicao is null then
        raise exception 'ERRO : Variavel de sessao [DB_instit] nao encontrada.';
    end if;

    for rDados in (
        select e91_numemp as empenho,
               (round(e91_vlremp, 2) - round(e91_vlranu, 2) - round(e91_vlrliq, 2)) as valor,
               o206_recurso
        from empenho.empresto
        join empenho.empempenho on e60_numemp = e91_numemp
        join orcamento.origemcomplementorecurso on o206_origem = 10 and o206_numero = e60_numemp
        where e91_anousu = anousu
          and e60_anousu = (anousu - 1)::int
          and e60_instit = instituicao
          and (round(e91_vlremp, 2) - round(e91_vlranu, 2) - round(e91_vlrliq, 2)) > 0
    ) loop

        retorno.empenho = rDados.empenho;
        retorno.valor = rDados.valor;
        retorno.codigo_recurso = rDados.o206_recurso;

        return next retorno;
        end loop;
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
