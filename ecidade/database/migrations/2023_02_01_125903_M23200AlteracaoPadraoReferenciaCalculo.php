<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M23200AlteracaoPadraoReferenciaCalculo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<SQL
    ALTER TABLE empenho.retencaoreceitasadicionais
     ALTER COLUMN e19_indvalorbase SET DEFAULT TRUE

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
    ALTER TABLE empenho.retencaoreceitasadicionais
     ALTER COLUMN e19_indvalorbase SET DEFAULT FALSE

SQL;
        DB::connection()->getPdo()->exec($sql);
    }
}
