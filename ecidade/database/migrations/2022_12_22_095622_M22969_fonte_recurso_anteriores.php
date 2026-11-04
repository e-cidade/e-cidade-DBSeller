<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M22969FonteRecursoAnteriores extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // corrige a migracao anterior
        DB::connection()->getPdo()->exec(<<<SQL
update fonterecurso set codigo_siconfi = o15_codigosiconfi
  from orctiporec
 where orctiporec_id = o15_codigo
    and exercicio < 2022;
SQL
        );

        $menor = DB::table('orcdotacao')->select(DB::raw('min(o58_anousu)'))->first()->min;
        $ate = 2017;
        $migrar = range($menor, $ate);

        foreach ($migrar as $exercicio) {
            DB::connection()->getPdo()->exec(<<<SQL
insert into orcamento.fonterecurso (orctiporec_id, exercicio, codigo_siconfi, gestao, classificacaofr_id, tipo_detalhamento, descricao)
select o15_codigo,
       $exercicio,
       o15_codigosiconfi,
       o15_recurso,
       1,
       case when o15_loatipo is null or o15_loatipo = 0 then '00' else lpad(o15_loatipo, 2, 0) end as detalhamento,
       o15_descr
  from orctiporec;
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
        DB::connection()->getPdo()->exec('delete from orcamento.fonterecurso where exercicio < 2018');
    }
}
