<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M25095AtualizadoQueryDDR extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
update contabilidade.conplanoinfocomplementar
set c121_sql = 'SELECT o201_orctiporec
  FROM conlancamcomplementorecurso
  join contabilidade.conlancamdoc on c71_codlan = o201_codlan
  join contabilidade.conhistdoc on c71_coddoc = c53_coddoc
 WHERE o201_codlan = codigo_lancamento
 and c53_tipo in (
         10, 11, 20, 21, 30, 31, 40, 41, 50, 51, 60, 61, 70, 71, 90, 91, 92, 100, 101, 110,
         111, 112, 113, 200, 201, 414, 415, 900, 901, 1000, 1500, 2000, 2001
     )
 union
 SELECT c130_orctiporec
   FROM conlancamrecurso
  WHERE c130_conlancam = codigo_lancamento
    AND c130_conta = conta_reduzida
    AND c130_natureza = natureza
 LIMIT 1'
where c121_sequencial= 100;
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
update contabilidade.conplanoinfocomplementar
set c121_sql = ' SELECT o201_orctiporec
  FROM conlancamcomplementorecurso
 WHERE o201_codlan = codigo_lancamento
 union
 SELECT c130_orctiporec
   FROM conlancamrecurso
  WHERE c130_conlancam = codigo_lancamento
    AND c130_conta = conta_reduzida
    AND c130_natureza = natureza
 LIMIT 1'
where c121_sequencial= 100;
SQL
        );
    }
}
