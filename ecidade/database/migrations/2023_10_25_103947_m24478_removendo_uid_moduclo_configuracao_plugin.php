<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M24478RemovendoUidModucloConfiguracaoPlugin extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $plugin = DB::table('configuracoes.db_plugin')->where('db145_nome', 'matricula-on-line')->get();
        if ($plugin->count() > 0) {
            $uids = [
                "facil-5d88b16ab2a76",
                "facil-5d88b1c2bec62",
                "facil-5dcadda3f3e4c",
                "facil-5dd59b9d92eb1",
                "facil-bh92-3369-9gkj-730f4cg2d7d",
                "facil-2f7f19c77bg793"
            ];

            DB::table('configuracoes.db_pluginitensmenu')->whereIn('db146_uid', $uids)->where('db146_db_plugin', $plugin->first()->db145_sequencial)->delete();
        }
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
