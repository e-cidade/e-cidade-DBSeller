<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M24237AlteraIndiceRhpessoalmov extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<SQL
        drop index rhpessoalmov_ano_mes_reg_ind;
        create unique index IF NOT EXISTS rhpessoalmov_ano_mes_reg_uk on rhpessoalmov (rh02_anousu, rh02_mesusu, rh02_regist);
        analyze rhpessoalmov;
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
        drop index rhpessoalmov_ano_mes_reg_uk;
        create index IF NOT EXISTS rhpessoalmov_ano_mes_reg_ind on rhpessoalmov (rh02_anousu, rh02_mesusu, rh02_regist);
        analyze rhpessoalmov;
SQL;
        DB::connection()->getPdo()->exec($sql);
    }
}
