<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M26248AtualizaConhistdocregra extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
update contabilidade.conhistdocregra
   set c92_regra = 'select * from doc_2032_abertura()'
 where c92_conhistdoc = 2032
   and c92_anousu = 2023;

update contabilidade.conhistdocregra
   set c92_regra = 'select * from doc_2033_abertura()'
 where c92_conhistdoc = 2033
   and c92_anousu = 2023;

update contabilidade.conhistdocregra
   set c92_regra = 'select 1'
 where c92_conhistdoc = 2021
   and c92_anousu = 2023;
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
update contabilidade.conhistdocregra
   set c92_regra = 'SELECT e91_numemp AS empenho,
               (round(e91_vlremp, 2) - round(e91_vlranu, 2) - round(e91_vlrliq, 2)) AS valor_credito,
                0 AS valor_debito,
                (round(e91_vlremp, 2) - round(e91_vlranu, 2) - round(e91_vlrliq, 2)) AS valor,
                ''f'' AS inverter_lancamento
         FROM empresto
         JOIN empempenho ON e60_numemp = e91_numemp
         WHERE e91_anousu = fc_getsession(''DB_anousu'')::int
           AND e60_anousu = fc_getsession(''DB_anousu'')::int -1
           AND e60_instit = fc_getsession(''DB_instit'')::int
           AND (round(e91_vlremp, 2) - round(e91_vlranu, 2) - round(e91_vlrliq, 2)) > 0;'
 where c92_conhistdoc = 2032
   and c92_anousu = 2023;

update contabilidade.conhistdocregra
   set c92_regra = 'SELECT e91_numemp AS empenho,
                (round(e91_vlrliq, 2) - round(e91_vlrpag, 2)) AS valor_credito,
                0 AS valor_debito,
                (round(e91_vlrliq, 2) - round(e91_vlrpag, 2)) AS valor,
                ''f'' AS inverter_lancamento
         FROM empresto
         JOIN empempenho ON e60_numemp = e91_numemp
         WHERE e91_anousu = fc_getsession(''DB_anousu'')::int
           AND e60_anousu = fc_getsession(''DB_anousu'')::int -1
           AND e60_instit = fc_getsession(''DB_instit'')::int
           AND (round(e91_vlrliq, 2) - round(e91_vlrpag, 2) ) > 0;'
 where c92_conhistdoc = 2033
   and c92_anousu = 2023;
SQL
        );
    }
}
