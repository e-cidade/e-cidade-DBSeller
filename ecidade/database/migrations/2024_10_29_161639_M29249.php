<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M29249 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(
            <<<SQL

INSERT INTO tipoatojuridico (e165_sequencial, e165_descricao, e165_ativo) VALUES (0, 'Ausênsia de Ato Jurdico', true);

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
        DB::connection()->getPdo()->exec(
            <<<SQL
DELETE FROM tipoatojuridico WHERE e165_sequencial = 0;

SQL
        );
    }
}
