<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M28744CriaIndicesProcessamentoPonto extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<SQL
            create index pontoeletronicoarquivodata_matricula_data_in on recursoshumanos.pontoeletronicoarquivodata(rh197_matricula, rh197_data);
            create index pontoeletronicoarquivodataregistro_pontoarquivodata_in on recursoshumanos.pontoeletronicoarquivodataregistro(rh198_pontoeletronicoarquivodata);
            create index assenta_regist_in on recursoshumanos.assenta(h16_regist);
            create index assenta_regist_datas_in on recursoshumanos.assenta(h16_regist, h16_dtterm, h16_dtconc);
            drop index pontoeletronicoarquivodata_matricula_in;
            drop index pontoeletronicoarquivodata_data_in;
            drop index assenta_reg_ass_in;
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
            create index pontoeletronicoarquivodata_matricula_in on recursoshumanos.pontoeletronicoarquivodata(rh197_matricula);
            create index pontoeletronicoarquivodata_data_in on recursoshumanos.pontoeletronicoarquivodata(rh197_data);
            create index assenta_reg_ass_in on recursoshumanos.assenta(h16_regist, h16_assent);
            drop index pontoeletronicoarquivodata_matricula_data_in;
            drop index pontoeletronicoarquivodataregistro_pontoarquivodata_in;
            drop index assenta_regist_in;
            drop index assenta_regist_datas_in;
SQL;
        DB::connection()->getPdo()->exec($sql);
    }
}
