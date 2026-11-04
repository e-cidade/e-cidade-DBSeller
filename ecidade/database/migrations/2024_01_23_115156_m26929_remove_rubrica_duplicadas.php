<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class M26929RemoveRubricaDuplicadas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
        create temp table temp_rubricas as (select distinct on(eso26_rubrica,eso26_instituicao) * from esocial.esocialrubricas);
        delete from esocial.esocialrubricas;
        insert into esocial.esocialrubricas select * from temp_rubricas;
        create unique index rub_inst on esocial.esocialrubricas (trim(upper(eso26_rubrica)),eso26_instituicao);
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
        return 0;
    }
}
