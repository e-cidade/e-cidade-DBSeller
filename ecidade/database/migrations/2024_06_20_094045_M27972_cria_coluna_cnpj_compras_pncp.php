<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M27972CriaColunaCnpjComprasPncp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pncp.compraspncp', function (Blueprint $bens) {
            $bens->string('pn03_cnpj', 32)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pncp.compraspncp', function (Blueprint $bens) {
            $bens->dropColumn('pn03_cnpj');
        });
    }
}
