<?php

use Illuminate\Database\Migrations\Migration;

class M27275ViewRecursoExercicio extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(<<<SQL
drop view if exists contabilidade.recurso_exercicio;

create view contabilidade.recurso_exercicio as
select orctiporec.o15_codigo,
       fonterecurso.codigo_siconfi,
       fonterecurso.gestao,
       orctiporec.o15_recurso,
       orctiporec.o15_complemento,
       orctiporec.o15_descr,
       fonterecurso.descricao,
       fonterecurso.classificacaofr_id,
       fonterecurso.tipo_detalhamento,
       fonterecurso.exercicio
from orcamento.orctiporec
join orcamento.fonterecurso on fonterecurso.orctiporec_id = orctiporec.o15_codigo;
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
        DB::unprepared(<<<SQL
drop view if exists contabilidade.recurso_exercicio;
SQL
        );
    }
}
