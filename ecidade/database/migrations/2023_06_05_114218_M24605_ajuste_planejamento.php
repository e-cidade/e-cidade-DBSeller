<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M24605AjustePlanejamento extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
update planejamento.estimativareceita set esferaorcamentaria = 10 where esferaorcamentaria = 0;
update planejamento.detalhamentoiniciativa set pl20_esferaorcamentaria = 10 where pl20_esferaorcamentaria = 0;
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
