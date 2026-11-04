<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M23809RemovendoCampoCursoProfissionalizanteTabelaEnsinoInserindoNaTabelaCursoedu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->upDicionario();
        Schema::table('escola.ensino', function (Blueprint $table) {
            $table->dropColumn('ed10_censocursoprofiss');
        });
        Schema::table('escola.cursoedu', function (Blueprint $table) {
            $table->integer('ed29_censocursoprofiss')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->downDicionario();
        Schema::table('escola.ensino', function (Blueprint $table) {
            $table->integer('ed10_censocursoprofiss')->nullable();
        });

        Schema::table('escola.cursoedu', function (Blueprint $table) {
            $table->dropColumn('ed29_censocursoprofiss');
        });
    }

    public function upDicionario()
    {
        DB::table('configuracoes.db_sysarqcamp')
            ->where('codcam', 21844)
            ->where('codarq', 1010045)->delete();

        DB::table('configuracoes.db_sysarqcamp')->insert([
            'codarq' => 1010048,
            'codcam' => 21844,
            'seqarq' => 7,
            'codsequencia' => 0
        ]);

        DB::table('configuracoes.db_syscampo')->where('codcam', 21844)->update([
            'nomecam' => 'ed29_censocursoprofiss'
        ]);
    }

    public function downDicionario()
    {
        DB::table('configuracoes.db_sysarqcamp')->insert([
            'codarq' => 1010045,
            'codcam' => 21844,
            'seqarq' => 8,
            'codsequencia' => 0
        ]);

        DB::table('configuracoes.db_sysarqcamp')
            ->where('codcam', 21844)
            ->where('codarq', 1010048)->delete();

        DB::table('configuracoes.db_syscampo')->where('codcam', 21844)->update([
            'nomecam' => 'ed10_censocursoprofiss'
        ]);
    }
}
