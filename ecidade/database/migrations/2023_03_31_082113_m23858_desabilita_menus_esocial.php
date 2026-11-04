<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M23858DesabilitaMenusEsocial extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<SQL
            update configuracoes.db_itensmenu set libcliente = false where id_item in (select id_item_filho from configuracoes.db_menu where id_item = 10569);
            update configuracoes.db_itensmenu set libcliente = false where id_item in (
                10569,
                10586,
                10483,
                10484,
                10485,
                10573,
                228091,
                228097,
                10525,
                10514
            );
SQL;
        DB::connection()->getPdo()->exec($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $sql = <<<SQL
            update configuracoes.db_itensmenu set libcliente = true where id_item in (select id_item_filho from configuracoes.db_menu where id_item = 10569);
            update configuracoes.db_itensmenu set libcliente = true where id_item in (
                10569,
                10586,
                10483,
                10484,
                10485,
                10573,
                228091,
                228097,
                10525,
                10514
            );
SQL;
        DB::connection()->getPdo()->exec($sql);
    }
}
