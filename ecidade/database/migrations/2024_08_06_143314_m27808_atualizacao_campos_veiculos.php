<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M27808AtualizacaoCamposVeiculos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(<<<SQL
            ALTER TABLE veicmanut ALTER COLUMN ve62_veiccadtiposervico DROP NOT NULL;
            ALTER TABLE veicmanut ALTER COLUMN ve62_veiccadtiposervico SET DEFAULT 0;
            ALTER TABLE veicmanut ALTER COLUMN ve62_medida SET DEFAULT 0;
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
        //
    }
}
