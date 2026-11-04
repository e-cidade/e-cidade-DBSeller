<?php

use Illuminate\Database\Migrations\Migration;

class M27177AjusteConta9PlanoreceitaUF extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
insert into planoreceita
select
    nextval('planoreceita_id_seq'::regclass),
    2024,
    uniao,
    conta,
    nome,
    funcao,
    sintetica,
    classe,
    categoria,
    origem,
    especie,
    desdobramento1,
    desdobramento2,
    desdobramento3,
    tipo,
    desdobramento4,
    desdobramento5,
    desdobramento6,
    now(),
    now()
 from planoreceita
  where uniao is false and exercicio = 2023 and conta like '900000000000000' order by conta;
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
delete from planoreceita where uniao is false and exercicio = 2024 and conta like '900000000000000';
SQL
        );
    }
}
