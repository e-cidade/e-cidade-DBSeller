<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M24520AjusteTamanhoCampoO46Codlei extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
	    $sql = <<<SQL
update db_syscampo set tamanho = 6 where codcam = 5330;
SQL;
	    DB::connection()->getPdo()->exec($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
	    $sql = <<<SQL
update db_syscampo set tamanho = 4 where codcam = 5330;
SQL;
	    DB::connection()->getPdo()->exec($sql);
    }
}
