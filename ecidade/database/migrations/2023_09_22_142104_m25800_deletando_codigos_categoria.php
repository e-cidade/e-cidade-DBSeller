<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M25800DeletandoCodigosCategoria extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<SQL
                delete from rhcodigocategoria where rh255_codigo in (107,108,308);
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
            insert into rhcodigocategoria VALUES(107,'Contrato de trabalho Verde e Amarelo - sem acordo para antecipação mensal da multa rescisória do FGTS');
            insert into rhcodigocategoria VALUES(108,'Contrato de trabalho Verde e Amarelo - com acordo para antecipação mensal da multa rescisória do FGTS');
            insert into rhcodigocategoria VALUES(308,'Conscrito');
SQL;

        DB::connection()->getPdo()->exec($sql);
    }
}
