<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M24561Conta9PlanoRs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
insert into planoreceita (exercicio, uniao, conta, nome, funcao, sintetica, classe, categoria, origem, especie, desdobramento1, desdobramento2, desdobramento3, tipo, desdobramento4, desdobramento5, desdobramento6, created_at, updated_at)
values (
2023,
false,
'900000000000000',
'( R ) Deduções da Receita',
'Agrega os registros referentes às deduções das Receitas Orçamentárias, que são representadas pelo dígito "9" inserido no início do código da conta, sem suprimir nenhum outro dígito da codificação.',
true,
9,
1,
0,
0,
'0',
'00',
'0',
0,
'00',
'00',
'00',
to_char(now(), 'YYYY-MM-DD HH:MI:SS')::timestamp,
to_char(now(), 'YYYY-MM-DD HH:MI:SS')::timestamp
);
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
delete from planoreceita where exercicio = 2023 and uniao is false and conta = '900000000000000';
SQL
        );
    }
}
