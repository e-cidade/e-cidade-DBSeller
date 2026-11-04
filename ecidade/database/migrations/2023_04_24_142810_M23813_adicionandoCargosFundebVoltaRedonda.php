<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;


class M23813adicionandoCargosFundebVoltaRedonda extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<SQL
                INSERT INTO pessoal.rhcargosfundeb (rh283_codigo, rh283_descricao, rh283_quantidade) values (0, 'Assentamento', 0);
                INSERT INTO pessoal.rhcargosfundeb (rh283_codigo, rh283_descricao, rh283_quantidade) values (1, 'Diretor', 4);
                INSERT INTO pessoal.rhcargosfundeb (rh283_codigo, rh283_descricao, rh283_quantidade) values (2, 'Supervisor', 3);
                INSERT INTO pessoal.rhcargosfundeb (rh283_codigo, rh283_descricao, rh283_quantidade) values (3, 'Professor', 2);
                INSERT INTO pessoal.rhcargosfundeb (rh283_codigo, rh283_descricao, rh283_quantidade) values (4, 'Apoio', 1);

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
             DELETE FROM pessoal.rhcargosfundeb WHERE rh283_codigo in (0,1,2,3,4);

SQL;
        DB::connection()->getPdo()->exec($sql);
    }
}
