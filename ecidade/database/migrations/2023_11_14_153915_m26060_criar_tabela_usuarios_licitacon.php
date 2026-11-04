<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M26060CriarTabelaUsuariosLicitacon extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('licitacao.licitaconusuarios', function (Blueprint $table) {
            $table->increments('l50_codigo');
            $table->string('l50_id_externo');
            $table->string('l50_chave');
            $table->integer('l50_instituicao')->unique();

            $table->foreign('l50_instituicao')->references('codigo')->on('configuracoes.db_config');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('licitacao.licitaconusuarios');
    }
}
