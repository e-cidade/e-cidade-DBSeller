<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M28957AlterTableCensoareaposgraduacaoNovaFriburgo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement(
            "ALTER TABLE IF EXISTS escola.censoareasposgraduacao RENAME TO censoareaspos;"
        );

        $indexExists = DB::select("
            SELECT indexname
            FROM pg_indexes
            WHERE tablename = 'censoareaspos'
            AND indexname = 'censoareasposgraduacao_pkey';
        ");

        if (!empty($indexExists)) {
            DB::statement("ALTER INDEX censoareasposgraduacao_pkey RENAME TO censoareaspos_pkey;");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE IF EXISTS escola.censoareaspos RENAME TO censoareasposgraduacao;");

        $indexExists = DB::select("
            SELECT indexname
            FROM pg_indexes
            WHERE tablename = 'censoareasposgraduacao'
            AND indexname = 'censoareaspos_pkey';
        ");

        if (!empty($indexExists)) {
            DB::statement("ALTER INDEX censoareaspos_pkey RENAME TO censoareasposgraduacao_pkey;");
        }
    }
}
