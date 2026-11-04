<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M23511NovosTiposDeCalculos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<SQL
            INSERT INTO empenho.retencaotipocalc (e32_sequencial,e32_descricao) VALUES(8,'PIS/PASEP');
            INSERT INTO empenho.retencaotipocalc (e32_sequencial,e32_descricao) VALUES(9,'CSLL');
            INSERT INTO empenho.retencaotipocalc (e32_sequencial,e32_descricao) VALUES(10,'COFINS');
            INSERT INTO empenho.retencaotipocalc (e32_sequencial,e32_descricao) VALUES(11,'Agregados');
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
            DELETE FROM empenho.retencaotipocalc WHERE e32_sequencial = 8;
            DELETE FROM empenho.retencaotipocalc WHERE e32_sequencial = 9;
            DELETE FROM empenho.retencaotipocalc WHERE e32_sequencial = 10;
            DELETE FROM empenho.retencaotipocalc WHERE e32_sequencial = 11;
SQL;
        DB::connection()->getPdo()->exec($sql);
    }
}
