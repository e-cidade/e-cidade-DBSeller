<?php

use Illuminate\Database\Migrations\Migration;

class M29161CreateUniqueTermoinscrreg extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("create unique index if not exists uidx_termoiscrreg_coddiv_termo on divida.termoinscrreg(v93_termo, v93_coddiv)");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("drop index uidx_termoiscrreg_coddiv_termo");
    }
}
