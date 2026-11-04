<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M28987 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
UPDATE tipoatojuridico
SET e165_ativo = false
where e165_sequencial = 99;


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

UPDATE tipoatojuridico
SET e165_ativo = true
where e165_sequencial = 99;

SQL
        );
    }
}
