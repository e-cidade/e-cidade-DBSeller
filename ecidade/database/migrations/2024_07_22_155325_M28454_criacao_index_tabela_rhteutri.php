<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M28454CriacaoIndexTabelaRhteutri extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<SQL
            DROP INDEX rhteutri_rh67_anousu_rh67_mesusu_in;
            DROP INDEX rhteutri_reg_tipo_in;
            ALTER TABLE rhteutri ADD CONSTRAINT rhteutri_reg_tipo_ae_me_in UNIQUE (rh67_regist, rh67_rhtipovale, rh67_anousu, rh67_mesusu);

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
            DROP INDEX rhteutri_reg_tipo_ae_me_in;
            ALTER TABLE rhteutri ADD CONSTRAINT rhteutri_reg_tipo_in UNIQUE (rh67_regist, rh67_rhtipovale);
            ALTER TABLE rhteutri ADD CONSTRAINT rhteutri_rh67_anousu_rh67_mesusu_in UNIQUE (rh67_anousu, rh67_mesusu);
SQL;
        DB::connection()->getPdo()->exec($sql);

    }
}
