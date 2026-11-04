<?php

use Illuminate\Database\Migrations\Migration;

class M27820AcertoTipoliquidacaoSigfis extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
with dados as (select distinct e69_codnota
       from empnota
            inner join conlancamnota on c66_codnota = e69_codnota
            inner join conlancam on c70_codlan = c66_codlan
            inner join conlancamdoc on c71_codlan = c70_codlan
            inner join conhistdoc on c53_coddoc = c71_coddoc
            left join empnotasigfistipodocliquidacao  on e178_empnota = e69_codnota
       where c53_tipo = 20
         and c70_data >= '2024-01-01'
         and e178_empnota is null)
INSERT INTO empnotasigfistipodocliquidacao
 select nextval('empnotasigfistipodocliquidacao_e178_sequencial_seq'),
        e69_codnota,
        4 from dados;
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
