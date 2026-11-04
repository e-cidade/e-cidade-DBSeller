<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M23511AdicionaEventosR4000 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<SQL
            -- R-2055
            insert into habitacao.avaliacao values (4000117, 5, 'R-2055 - AQ. Produtor Rural', 'R-2055 - AQ. Produtor Rural', true, 'r2055-aqprodrural', null, false);
            insert into recursoshumanos.esocialformulariotipo values (47, 'R-2055 - AQ. Produtor Rural');
            insert into efdreinfversaoformulario values (nextval('efdreinfversaoformulario_efd03_sequencial_seq'), '1.5.1', 4000117, 47);

            -- R-4010
            insert into habitacao.avaliacao values (4000118, 5, 'R-4010 Pagamentos/créditos a beneficiário pessoa física', 'R-4010 Pagamentos/créditos a beneficiário pessoa física', true, 'r4010-pagcredpf', null, false);
            insert into recursoshumanos.esocialformulariotipo values (52, 'R-4010 Pagamentos/créditos a beneficiário pessoa física');
            insert into efdreinfversaoformulario values (nextval('efdreinfversaoformulario_efd03_sequencial_seq'), '1.5.1', 4000118, 52);

            -- R-4020
            insert into habitacao.avaliacao values (4000119, 5, 'R-4020 Pagamentos/créditos a beneficiário pessoa jurídica', 'R-4020 Pagamentos/créditos a beneficiário pessoa jurídica', true, 'r4020-pagcredpj', null, false);
            insert into recursoshumanos.esocialformulariotipo values (53, 'R-4020 Pagamentos/créditos a beneficiário pessoa jurídica');
            insert into efdreinfversaoformulario values (nextval('efdreinfversaoformulario_efd03_sequencial_seq'), '1.5.1', 4000119, 53);

            -- R-4040
            insert into habitacao.avaliacao values (4000120, 5, 'R-4040 Pagamentos/créditos a beneficiários não identificados', 'R-4040 Pagamentos/créditos a beneficiários não identificados', true, 'r4020-pagcredni', null, false);
            insert into recursoshumanos.esocialformulariotipo values (54, 'R-4040 Pagamentos/créditos a beneficiários não identificados');
            insert into efdreinfversaoformulario values (nextval('efdreinfversaoformulario_efd03_sequencial_seq'), '1.5.1', 4000120, 54);

            -- R-4099
            insert into habitacao.avaliacao values (4000121, 5, 'R-4099 Fechamento/Reabertura dos eventos da série R-4000', 'R-4099 Fechamento/Reabertura dos eventos da série R-4000', true, 'r4099-reabfechr4000', null, false);
            insert into recursoshumanos.esocialformulariotipo values (55, 'R-4099 Fechamento/Reabertura dos eventos da série R-4000');
            insert into efdreinfversaoformulario values (nextval('efdreinfversaoformulario_efd03_sequencial_seq'), '1.5.1', 4000121, 55);
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
            delete from efdreinfversaoformulario where efd03_esocialformulariotipo in (47, 52, 53, 54, 55);
            delete from recursoshumanos.esocialformulariotipo where rh209_sequencial in (47, 52, 53, 54, 55);
            delete from habitacao.avaliacao where db101_sequencial in (4000117, 4000118, 4000119, 4000120, 4000121);
SQL;
        DB::connection()->getPdo()->exec($sql);
    }
}
