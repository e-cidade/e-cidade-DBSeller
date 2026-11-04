<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M22969AtualizaPlEstruturaldotacao extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
create or replace function fc_estruturaldotacao(integer,integer)
returns varchar
as $$
declare

    anousu alias for $1;
    coddot alias for $2;
    estrutural varchar(200);

begin

select lpad(o58_orgao, 2, 0) || '.' ||
       lpad(o58_unidade, 2, 0) || '.' ||
       lpad(o58_funcao, 2, 0) || '.' ||
       lpad(o58_subfuncao, 3, 0) || '.' ||
       lpad(o58_programa, 4, 0) || '.' ||
       lpad(o58_projativ, 4, 0) || '.' ||
       lpad(o56_elemento, 13, 0) || '.' ||
       gestao || '.' ||
       lpad(o15_complemento, 4, 0)

      into estrutural
      from orcdotacao d
      join orcelemento o on o.o56_codele = d.o58_codele
           and o.o56_anousu = d.o58_anousu
      join orctiporec on orctiporec.o15_codigo = d.o58_codigo
      join fonterecurso on orctiporec_id = o15_codigo
           and exercicio = d.o58_anousu
      where d.o58_anousu = anousu
        and d.o58_coddot = coddot;

    return estrutural;

end;
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
        DB::connection()->getPdo()->exec(<<<SQL
create or replace function fc_estruturaldotacao(integer,integer)
returns varchar
as $$
declare

    anousu alias for $1;
    coddot alias for $2;
    estrutural varchar(200);

begin

select lpad(o58_orgao, 2, 0) || '.' ||
       lpad(o58_unidade, 2, 0) || '.' ||
       lpad(o58_funcao, 2, 0) || '.' ||
       lpad(o58_subfuncao, 3, 0) || '.' ||
       lpad(o58_programa, 4, 0) || '.' ||
       lpad(o58_projativ, 4, 0) || '.' ||
       lpad(o56_elemento, 13, 0) || '.' ||
       o15_recurso
      into estrutural
      from orcdotacao d
      join orcelemento o on o.o56_codele = d.o58_codele
           and o.o56_anousu = d.o58_anousu
      join orctiporec on orctiporec.o15_codigo = d.o58_codigo

      where d.o58_anousu = anousu
        and d.o58_coddot = coddot;

    return estrutural;

end;
$$
language 'plpgsql';
SQL
        );
    }
}
