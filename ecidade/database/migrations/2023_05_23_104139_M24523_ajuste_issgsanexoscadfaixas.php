<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;


class M24523AjusteIssgsanexoscadfaixas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
            update issgsanexoscadfaixas set q161_valorfinal = 5760000 where q161_sequencial = 6;        
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

        update issgsanexoscadfaixas set q161_valorfinal = 4800000 where q161_sequencial = 6;        
SQL
        );
    }
}
