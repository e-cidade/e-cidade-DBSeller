<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M28531AjusteLinhas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(<<<SQL
update orcamento.orcparamseq
   set o69_descr = 'VARIA플O DO SALDO RPP (XLIV) = (XLIb - XLIa)', o69_labelrel = 'VARIA플O DO SALDO RPP (XLIV) = (XLIb - XLIa)'
 where o69_codparamrel = 271 and o69_ordem in (81,86);

 update orcamento.orcparamseq
   set o69_descr = 'OUTROS AJUSTES (XLIX)', o69_labelrel = 'OUTROS AJUSTES (XLIX)'
 where o69_codparamrel = 271 and o69_ordem in (81,86);
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
update orcamento.orcparamseq
   set o69_descr = 'VARIA플O DO SALDO RPP (XLIV) = (XLIa - XLIb)', o69_labelrel = 'VARIA플O DO SALDO RPP (XLIV) = (XLIa - XLIb)'
 where o69_codparamrel = 271 and o69_ordem in (81,86);

 update orcamento.orcparamseq
   set o69_descr = 'OUTROS AJUSTES (XLXIX)', o69_labelrel = 'OUTROS AJUSTES (XLXIX)'
 where o69_codparamrel = 271 and o69_ordem in (81,86);
SQL
        );
    }
}
