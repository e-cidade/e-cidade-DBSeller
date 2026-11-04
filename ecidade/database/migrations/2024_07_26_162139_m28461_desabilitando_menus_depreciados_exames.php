<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class M28461DesabilitandoMenusDepreciadosExames extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
DB::unprepared(<<<SQL
ALTER TABLE ambulatorial.sau_prestadorhorarios DROP CONSTRAINT  IF EXISTS sau_prestadorhorarios_diasemana_fk;
UPDATE db_itensmenu SET libcliente = 'f' WHERE id_item in (7691,7692,7693,7694,7225,7226,7227,7228,7229,7230,7231,7232,7695,7696,7697,7698);
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
DB::unprepared(<<<SQL
UPDATE db_itensmenu SET libcliente = 't' WHERE id_item in (7691,7692,7693,7694,7225,7226,7227,7228,7229,7230,7231,7232,7695,7696,7697,7698);
SQL
        );
    }
}
