<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M27603CriandoIndicesTabelaPadrao extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<SQL
            CREATE UNIQUE INDEX padroes_ano_mes_regime_codigo_instit_index
                ON pessoal.padroes (r02_anousu, r02_mesusu, r02_regime, TRIM(UPPER(r02_codigo)), r02_instit);
            alter table if exists contracheques_batches set schema pessoal;
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
            DROP INDEX IF EXISTS padroes_ano_mes_regime_codigo_instit_index;
            ALTER TABLE if EXISTS contracheques_batches SET SCHEMA public;
SQL;
        DB::connection()->getPdo()->exec($sql);

    }
}
