<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M25294AdicionarCamposNumpref extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL

        ALTER TABLE caixa.numpref ADD COLUMN k03_encargoscarneunica BOOLEAN NOT NULL DEFAULT TRUE;

        insert into db_syscampo values(1015518,'k03_encargoscarneunica','bool','Se o parâmetro estiver configurado = SIM, o sistema deverá ter o comportamento que já existe atualmente, ou seja, gerar a cota única aplicando multa e juros sobre as parcelas que já estão vencidas ','f', 'Emissão de Cota Unica ',1,'f','f','f',5,'text','Emissão de Cota Unica ');
        delete from db_sysarqcamp where codarq = 318;
        insert into db_sysarqcamp values(318,1904,1,0);
        insert into db_sysarqcamp values(318,10716,2,0);
        insert into db_sysarqcamp values(318,1905,3,17);
        insert into db_sysarqcamp values(318,1906,4,0);
        insert into db_sysarqcamp values(318,1907,5,0);
        insert into db_sysarqcamp values(318,1908,6,0);
        insert into db_sysarqcamp values(318,1909,7,0);
        insert into db_sysarqcamp values(318,1910,8,0);
        insert into db_sysarqcamp values(318,1911,9,0);
        insert into db_sysarqcamp values(318,1912,10,0);
        insert into db_sysarqcamp values(318,1913,11,0);
        insert into db_sysarqcamp values(318,1914,12,0);
        insert into db_sysarqcamp values(318,1915,13,0);
        insert into db_sysarqcamp values(318,7918,14,0);
        insert into db_sysarqcamp values(318,7925,15,0);
        insert into db_sysarqcamp values(318,7943,16,0);
        insert into db_sysarqcamp values(318,8737,17,0);
        insert into db_sysarqcamp values(318,8797,18,0);
        insert into db_sysarqcamp values(318,8799,19,0);
        insert into db_sysarqcamp values(318,9419,20,0);
        insert into db_sysarqcamp values(318,11859,21,0);
        insert into db_sysarqcamp values(318,14400,22,0);
        insert into db_sysarqcamp values(318,14484,23,0);
        insert into db_sysarqcamp values(318,14587,24,0);
        insert into db_sysarqcamp values(318,15036,25,0);
        insert into db_sysarqcamp values(318,17195,26,0);
        insert into db_sysarqcamp values(318,17196,27,0);
        insert into db_sysarqcamp values(318,17943,28,0);
        insert into db_sysarqcamp values(318,18059,29,0);
        insert into db_sysarqcamp values(318,18150,30,0);
        insert into db_sysarqcamp values(318,18429,31,0);
        insert into db_sysarqcamp values(318,18468,32,0);
        insert into db_sysarqcamp values(318,18874,33,0);
        insert into db_sysarqcamp values(318,19223,34,0);
        insert into db_sysarqcamp values(318,19647,35,0);
        insert into db_sysarqcamp values(318,20614,36,0);
        insert into db_sysarqcamp values(318,20230,37,0);
        insert into db_sysarqcamp values(318,1010703,38,0);
        insert into db_sysarqcamp values(318,1010704,39,0);
        insert into db_sysarqcamp values(318,1010705,40,0);
        insert into db_sysarqcamp values(318,1010706,41,0);
        insert into db_sysarqcamp values(318,1010707,42,0);
        insert into db_sysarqcamp values(318,1010708,43,0);
        insert into db_sysarqcamp values(318,1010709,44,0);
        insert into db_sysarqcamp values(318,1010710,45,0);
        insert into db_sysarqcamp values(318,1010711,46,0);
        insert into db_sysarqcamp values(318,1010712,47,0);
        insert into db_sysarqcamp values(318,1010713,48,0);
        insert into db_sysarqcamp values(318,1010714,49,0);
        insert into db_sysarqcamp values(318,1010715,50,0);
        insert into db_sysarqcamp values(318,1010716,51,0);
        insert into db_sysarqcamp values(318,1010717,52,0);
        insert into db_sysarqcamp values(318,1010718,53,0);
        insert into db_sysarqcamp values(318,1010719,54,0);
        insert into db_sysarqcamp values(318,1010720,55,0);
        insert into db_sysarqcamp values(318,1010721,56,0);
        insert into db_sysarqcamp values(318,1010722,57,0);
        insert into db_sysarqcamp values(318,1010723,58,0);
        insert into db_sysarqcamp values(318,1010724,59,0);
        insert into db_sysarqcamp values(318,1010725,60,0);
        insert into db_sysarqcamp values(318,1010726,61,0);
        insert into db_sysarqcamp values(318,1010727,62,0);
        insert into db_sysarqcamp values(318,1010728,63,0);
        insert into db_sysarqcamp values(318,1010729,64,0);
        insert into db_sysarqcamp values(318,1010730,65,0);
        insert into db_sysarqcamp values(318,1010731,66,0);
        insert into db_sysarqcamp values(318,1010732,67,0);
        insert into db_sysarqcamp values(318,1010733,68,0);
        insert into db_sysarqcamp values(318,1010734,69,0);
        insert into db_sysarqcamp values(318,1010736,70,0);
        insert into db_sysarqcamp values(318,1010737,71,0);
        insert into db_sysarqcamp values(318,1010738,72,0);
        insert into db_sysarqcamp values(318,1010739,73,0);
        insert into db_sysarqcamp values(318,1011819,74,0);
        insert into db_sysarqcamp values(318,1011820,75,0);
        insert into db_sysarqcamp values(318,1013157,76,0);
        insert into db_sysarqcamp values(318,1014503,77,0);
        insert into db_sysarqcamp values(318,1015464,78,0);
        insert into db_sysarqcamp values(318,1015518,81,0);
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

            ALTER TABLE caixa.numpref DROP COLUMN k03_encargoscarneunica;

            DELETE FROM configuracoes.db_sysarqcamp WHERE codcam=1015518 AND codarq=318 AND seqarq=81;

            DELETE FROM configuracoes.db_syscampo WHERE codcam=1015518;
SQL
        );
    }
}
