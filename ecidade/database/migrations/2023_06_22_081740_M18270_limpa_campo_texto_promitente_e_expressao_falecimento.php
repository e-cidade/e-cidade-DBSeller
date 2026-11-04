<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class M18270LimpaCampoTextoPromitenteEExpressaoFalecimento extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = "
                update cfiptu set j18_textoprom = '' where j18_anousu in (select j18_anousu from cfiptu order by j18_anousu desc limit 1);
                update pardiv set v04_expfalecimentocda = '';
        ";

        DB::connection()->getPdo()->exec($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        return;
    }
}
