<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M26956RemoveObrigatoriedadeS2210 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
            update habitacao.avaliacaopergunta set db103_obrigatoria = 'f' where  db103_sequencial = 4000352;
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
            update habitacao.avaliacaopergunta set db103_obrigatoria = 't' where  db103_sequencial = 4000352;
SQL
        );
    }
}
