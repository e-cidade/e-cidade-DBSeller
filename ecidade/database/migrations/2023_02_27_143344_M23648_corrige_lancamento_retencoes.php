<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M23648CorrigeLancamentoRetencoes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
create temp table w_corrige_lancamento_retencoes as
select
       c70_codlan,
       c70_valor,
       c75_numemp,
       o15_codigo, o15_complemento, o201_sequencial as id_corrigir
  from conlancam
  join conlancamdoc on c71_codlan = c70_codlan
  join conlancamemp on c75_codlan = c70_codlan
  join conlancamrec on c74_codlan = c70_codlan
  join orcreceita on (o70_anousu, o70_codrec) = (c74_anousu, c74_codrec)
  join orctiporec on o15_codigo = o70_codigo
  join conlancamcomplementorecurso on o201_codlan = c70_codlan
 where c70_anousu = 2023
   and c71_coddoc = 6000
   -- and c70_codlan in (4529230, 4529299, 4529561, 4529538, 4529528, 4529609)
   -- and o70_codrec = 5278
   -- and o201_complemento = 3110
   and o201_orctiporec != o70_codigo;

update conlancamcomplementorecurso
   set o201_complemento = o15_complemento, o201_orctiporec = o15_codigo
  from w_corrige_lancamento_retencoes
  where o201_sequencial = id_corrigir;

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
