<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M24561EsferaOrcamentariaReceita extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
update orcreceita set o70_esferaorcamentaria = 10 where o70_anousu = 2023 and (o70_esferaorcamentaria is null or o70_esferaorcamentaria = 0);
SQL
        );
    }
}
