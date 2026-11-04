<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M22048AlteraDescNomesocial extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
            UPDATE db_syscampo SET descricao = 'Nome Social apenas para pessoas físicas' WHERE codcam = 1013640;
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
            UPDATE db_syscampo SET descricao = 'Nome Social para atender legislação' WHERE codcam = 1013640;
SQL
        );
    }
}
