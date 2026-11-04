<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M23809AlterandoDefaultTabelaCursoedu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE escola.cursoedu ALTER COLUMN ed29_i_codigo SET DEFAULT nextval('escola.cursoedu_ed29_i_codigo_seq')");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE escola.cursoedu ALTER COLUMN ed29_i_codigo SET DEFAULT 0");
    }
}
