<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M24307AcertoContasBancarias extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement(<<<SQL
create temp table w_contas_bancarias as
select cb.*
from (
   select max(c56_anousu) as exercicio, c56_codcon, c56_reduz
     from conplanocontabancaria
    group by 2,3
 ) as x
join conplanocontabancaria cb on cb.c56_codcon = x.c56_codcon
     and cb.c56_reduz = x.c56_reduz
     and cb.c56_anousu = x.exercicio
 where c56_anousu <= 2022;
SQL
        );

        $sql = <<<SQL
select c56_contabancaria, c56_codcon, c61_anousu, c56_reduz
  from conplanoreduz
  JOIN w_contas_bancarias ON conplanoreduz.c61_reduz = w_contas_bancarias.c56_reduz
       and conplanoreduz.c61_codcon = w_contas_bancarias.c56_codcon
       and conplanoreduz.c61_anousu > w_contas_bancarias.c56_anousu;
SQL;

        $incluirCB = [];
        $dados = DB::select($sql);
        foreach ($dados as $dado) {
            $incluir = [
                "nextval('conplanocontabancaria_c56_sequencial_seq')",
                $dado->c56_contabancaria,
                $dado->c56_codcon,
                $dado->c61_anousu,
                $dado->c56_reduz
            ];
            $incluirCB[] = sprintf('(%s)', implode(', ', $incluir));
        }


        if (count($incluirCB) > 0) {
            $sqlIncluirCB = sprintf(
                "insert into contabilidade.conplanocontabancaria values %s ;",
                implode(', ', $incluirCB)
            );

            DB::select($sqlIncluirCB);
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
