<?php

use Illuminate\Database\Migrations\Migration;

class M27557AtualizaPlRecursoContaLancamento extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
create or replace function contabilidade.fc_recurso_conta_lancamento(lancamento int, conta int, natureza char(1) )
    returns int
    language plpgsql
as $$
declare
    id_recurso int;
begin

    select c130_orctiporec as codigo_recurso
    into id_recurso
    from conlancam
    join conlancamdoc on conlancamdoc.c71_codlan = c70_codlan
    join conhistdoc on conhistdoc.c53_coddoc = conlancamdoc.c71_coddoc
    join contabilidade.conlancamrecurso on c130_conlancam = c70_codlan
         and c130_conta = conta
         and c130_natureza = natureza
    where c70_codlan = lancamento;

    return id_recurso;
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
create or replace function contabilidade.fc_recurso_conta_lancamento(lancamento int, conta int, natureza char(1) )
    returns int
    language plpgsql
as $$
declare
    id_recurso int;
begin

    select
        case
            when o201_orctiporec is not null
                and c53_tipo in (10, 11, 20, 21, 30, 31, 40, 41, 50, 51, 60, 61, 70, 71, 90, 91, 92, 100, 101, 110, 111, 112, 113, 200, 201, 414, 415, 900, 901, 1000, 1500, 2000, 2001)
                then o201_orctiporec
            else c130_orctiporec
            end as codigo_recurso
    into id_recurso
    from conlancam
    join conlancamdoc on conlancamdoc.c71_codlan = c70_codlan
    join conhistdoc on conhistdoc.c53_coddoc = conlancamdoc.c71_coddoc
    join contabilidade.conlancamrecurso on c130_conlancam = c70_codlan
         and c130_conta = conta
         and c130_natureza = natureza
    left join conlancamcomplementorecurso on o201_codlan = c70_codlan
    where c70_codlan = lancamento;

    return id_recurso;
end;
$$;
SQL
        );
    }
}
