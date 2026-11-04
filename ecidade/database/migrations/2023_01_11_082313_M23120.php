<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M23120 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
update db_syscampo set rotulo = 'Subrecurso', rotulorel = 'Subrecurso' where nomecam = 'o15_recurso';
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
        DB::connection()->getPdo()->exec(<<<SQL
update db_syscampo set rotulo = 'Recurso', rotulorel = 'Recurso' where nomecam = 'o15_recurso';
SQL
        );
    }
}
