<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M25867AjusteLinhaAnexoIVRGF extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
update orcparamseqorcparamseqcoluna set o116_formula = 'L[26]->ateperiodo+L[27]->ateperiodo+L[28]->ateperiodo+L[29]->ateperiodo+L[30]->ateperiodo' where  o116_codparamrel = 185  and o116_codseq = 25 and o116_ordem = 2;
update orcparamseqorcparamseqcoluna set o116_formula = 'L[26]->noperiodo+L[27]->noperiodo+L[28]->noperiodo+L[29]->noperiodo+L[30]->noperiodo' where  o116_codparamrel = 185  and o116_codseq = 25 and o116_ordem = 1;
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
update orcparamseqorcparamseqcoluna set o116_formula = 'L[26]->ateperiodo+L[27]->ateperiodo+L[29]->ateperiodo+L[30]->ateperiodo' where  o116_codparamrel = 185  and o116_codseq = 25 and o116_ordem = 2;
update orcparamseqorcparamseqcoluna set o116_formula = 'L[26]->noperiodo+L[27]->noperiodo+L[29]->noperiodo+L[30]->noperiodo' where  o116_codparamrel = 185  and o116_codseq = 25 and o116_ordem = 1;

SQL
        );           
    }
}
