<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M28384AlterandoItensMenuAntigoAndamentoBloqueandoParaClientes extends Migration
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
            update  configuracoes.db_itensmenu set libcliente = false where id_item = 2182;
            update  configuracoes.db_itensmenu set libcliente = false where id_item = 2184;
            update  configuracoes.db_itensmenu set libcliente = false where id_item = 608572;
            update  configuracoes.db_itensmenu set libcliente = false where id_item = 2175;
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
            update  configuracoes.db_itensmenu set libcliente = true where id_item = 2182;
            update  configuracoes.db_itensmenu set libcliente = true where id_item = 2184;
            update  configuracoes.db_itensmenu set libcliente = true where id_item = 608572;
            update  configuracoes.db_itensmenu set libcliente = true where id_item = 2175;
SQL
        );
    }
}
