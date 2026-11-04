<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M23809AcertoMediacaoDidatopdasgocica extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('escola.censoetapamediacaodidaticopedagogica')
            ->where('ed131_mediacaodidaticopedagogica', 2)
            ->update(['ed131_mediacaodidaticopedagogica' => 3]);

        DB::table('escola.ensino')
            ->where('ed10_mediacaodidaticopedagogica', 2)
            ->update(['ed10_mediacaodidaticopedagogica' => 3]);

        DB::table('escola.mediacaodidaticopedagogica')
            ->where('ed130_codigo', 2)
            ->delete();
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        return true;
    }
}
