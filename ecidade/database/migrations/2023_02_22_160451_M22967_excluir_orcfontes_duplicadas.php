<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M22967ExcluirOrcfontesDuplicadas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $exercicios = [2023, 2024, 2025];
        foreach ($exercicios as $exercicio) {
            DB::connection()->getPdo()->exec(<<<SQL
drop table if exists w_excluir_fontes;

create table w_excluir_fontes as
with duplicados as (
  select o57_anousu, o57_fonte, count(*)
    from orcfontes
   where o57_anousu = $exercicio group by 1,2 having(count(*)) > 1
), excluir as (
    select (select o57_codfon
              from orcfontes f
              left join conplanoorcamento on c60_codcon = f.o57_codfon
                   and c60_anousu = f.o57_anousu
             where f.o57_anousu = duplicados.o57_anousu
               and f.o57_fonte = duplicados.o57_fonte
               and c60_codcon is null
          ) as excluir,
          (select o57_codfon
              from orcfontes f
              join conplanoorcamento on c60_codcon = f.o57_codfon
                   and c60_anousu = f.o57_anousu
             where f.o57_anousu = duplicados.o57_anousu
               and f.o57_fonte = duplicados.o57_fonte
          ) as manter,
          f.o57_anousu,
          f.o57_fonte
      from duplicados
      join orcfontes f on f.o57_anousu = duplicados.o57_anousu
           and f.o57_fonte = duplicados.o57_fonte
      left join conplanoorcamento on c60_codcon = f.o57_codfon
             and c60_anousu = f.o57_anousu
     where c60_codcon is null
) select * from excluir;

update planejamento.estimativareceita set orcfontes_id = w_excluir_fontes.manter
  from w_excluir_fontes
  where w_excluir_fontes.excluir = estimativareceita.orcfontes_id
    and w_excluir_fontes.o57_anousu = estimativareceita.anoorcamento ;

update planejamento.fatorcorrecaoreceita set orcfontes_id = w_excluir_fontes.manter
  from w_excluir_fontes
  where w_excluir_fontes.excluir = fatorcorrecaoreceita.orcfontes_id
    and w_excluir_fontes.o57_anousu = fatorcorrecaoreceita.anoorcamento ;

delete from orcfontes
 using w_excluir_fontes
 where w_excluir_fontes.excluir = orcfontes.o57_codfon
   and w_excluir_fontes.o57_anousu = orcfontes.o57_anousu ;

SQL
            );
        }
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
