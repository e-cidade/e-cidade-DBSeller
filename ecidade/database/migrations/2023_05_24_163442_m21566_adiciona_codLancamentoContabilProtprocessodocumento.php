<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M21566AdicionaCodLancamentoContabilProtprocessodocumento extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('protprocessodocumento', function (Blueprint $table) {
            $table->bigInteger('p01_c70codlan')
                ->nullable()
                ->after('p01_documento_hash');
            $table->foreign('p01_c70codlan')->references('c70_codlan')->on('conlancam');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('protprocessodocumento', function (Blueprint $table) {
            $table->dropForeign(['p01_c70codlan']);
            $table->dropColumn('p01_c70codlan');
        });
    }
}
