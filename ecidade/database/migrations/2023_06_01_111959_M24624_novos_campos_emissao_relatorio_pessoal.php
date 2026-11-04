<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M24624NovosCamposEmissaoRelatorioPessoal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<SQL
insert into relrubcampos values (nextval('relrubcampos_rh120_sequencial_seq'), 'rh56_datainicio', 20, 10);
insert into relrubcampos values (nextval('relrubcampos_rh120_sequencial_seq'), 'rh56_datafim', 20, 10);
SQL;
        DB::connection()->getPdo()->exec($sql);
        //
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $sql = <<<SQL
delete from relrubcampos where trim(rh120_campo) = 'rh56_datainicio'; 
delete from relrubcampos where trim(rh120_campo) = 'rh56_datafim';
SQL;
        DB::connection()->getPdo()->exec($sql);
        //
    }
}
