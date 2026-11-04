<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M23809AdicinandoNovaOpcaoNecessidadeEspecial extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('escola.necessidade')->insert([
            'ed48_i_codigo' => 114,
            'ed48_c_descr' => 'VISÃO MONOCULAR'
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('escola.necessidade')->where('ed48_i_codigo', 114)->delete();
    }
}
