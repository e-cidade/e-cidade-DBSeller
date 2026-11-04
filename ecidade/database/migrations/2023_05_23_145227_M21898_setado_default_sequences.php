<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M21898SetadoDefaultSequences extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         DB::connection()->getPdo()->exec(<<<SQL
alter table contabilidade.conlancam alter column c70_codlan set default nextval('conlancam_c70_codlan_seq');
alter table contabilidade.conlancamval alter column c69_sequen set default nextval('conlancamval_c69_sequen_seq');
alter table contabilidade.conlancamrecurso alter column c130_sequencial set default nextval('conlancamrecurso_c130_sequencial_seq');
alter table contabilidade.conlancaminstit alter column c02_sequencial set default nextval('conlancaminstit_c02_sequencial_seq');
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
