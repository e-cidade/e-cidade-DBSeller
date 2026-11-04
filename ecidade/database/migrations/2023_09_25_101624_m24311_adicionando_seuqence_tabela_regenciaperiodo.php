<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M24311AdicionandoSeuqenceTabelaRegenciaperiodo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE escola.regenciaperiodo ALTER COLUMN ed78_i_codigo SET DEFAULT nextval('escola.regenciaperiodo_ed78_i_codigo_seq')");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE escola.regenciaperiodo ALTER COLUMN ed78_i_codigo SET DEFAULT 0");
    }
}
