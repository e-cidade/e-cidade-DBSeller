<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M24544AlteraLinha19ManualAnexoIVRGF extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec("update orcparamseq set o69_manual = true where o69_codparamrel = 218 and o69_ordem = 19");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::connection()->getPdo()->exec("update orcparamseq set o69_manual = false where o69_codparamrel = 218 and o69_ordem = 19");
    }
}
