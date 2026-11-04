<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M23640 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
insert into fonterecurso
select nextval('fonterecurso_id_seq'),
       orctiporec_id,
       2023,
       codigo_siconfi,
       o15_recurso,
       classificacaofr_id,
       tipo_detalhamento,
       descricao
  from fonterecurso
  join orctiporec on orctiporec.o15_codigo = fonterecurso.orctiporec_id
 where orctiporec_id in (
    select o15_codigo
      from orctiporec
      left join fonterecurso on orctiporec_id = o15_codigo
                and exercicio = 2023
    where fonterecurso.id is null
    )
   and exercicio = 2022
   and o15_codigo != 0;
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
