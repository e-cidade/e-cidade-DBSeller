<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M28780CorrecaoValoresNovaColuna extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(<<<SQL
            update cadastro.lote set j34_areaprivativa = j34_area, j34_areausocomum = 0;
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
            update cadastro.lote set j34_areaprivativa = null, j34_areausocomum = null;
SQL
        );
    }
}
