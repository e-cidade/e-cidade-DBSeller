<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M24118RegrasDocumentosViradaRp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        $sql = <<<SQL

           update conhistdocregra set c92_regra = '

           SELECT e91_numemp AS empenho,
               (round(e91_vlremp, 2) - round(e91_vlranu, 2) - round(e91_vlrliq, 2)) AS valor_credito,
                0 AS valor_debito,
                (round(e91_vlremp, 2) - round(e91_vlranu, 2) - round(e91_vlrliq, 2)) AS valor,
                \'f\' AS inverter_lancamento
         FROM empresto
         JOIN empempenho ON e60_numemp = e91_numemp
         WHERE e91_anousu = fc_getsession(\'DB_anousu\')::int
           AND e60_anousu = fc_getsession(\'DB_anousu\')::int -1
           AND e60_instit = fc_getsession(\'DB_instit\')::int
           AND (round(e91_vlremp, 2) - round(e91_vlranu, 2) - round(e91_vlrliq, 2)) > 0;

           '
           where  c92_conhistdoc = 2032;



           update conhistdocregra set c92_regra = '

           SELECT e91_numemp AS empenho,
                (round(e91_vlrliq, 2) - round(e91_vlrpag, 2)) AS valor_credito,
                0 AS valor_debito,
                (round(e91_vlrliq, 2) - round(e91_vlrpag, 2)) AS valor,
                \'f\' AS inverter_lancamento
         FROM empresto
         JOIN empempenho ON e60_numemp = e91_numemp
         WHERE e91_anousu = fc_getsession(\'DB_anousu\')::int
           AND e60_anousu = fc_getsession(\'DB_anousu\')::int -1
           AND e60_instit = fc_getsession(\'DB_instit\')::int
           AND (round(e91_vlrliq, 2) - round(e91_vlrpag, 2) ) > 0;


           '
           where  c92_conhistdoc = 2033;
SQL;

        DB::connection()->getPdo()->exec($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $sql = <<<SQL

           update conhistdocregra set c92_regra = '

              select * from fc_valores_abertura_empenho_rp(2032) where valor <> 0;

           '
           where  c92_conhistdoc = 2032;



           update conhistdocregra set c92_regra = '

              select * from fc_valores_abertura_empenho_rp(2033) where valor <> 0;

           '
           where  c92_conhistdoc = 2033;

SQL;

                DB::connection()->getPdo()->exec($sql);
    }
}
