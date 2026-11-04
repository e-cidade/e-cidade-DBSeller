<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M23821DropDbTutorial extends Migration
{
    /**
     * Run the migrations
     * @return void
     */
    public function up()
    {
        Schema::drop('configuracoes.db_tutorialetapapassos');
        Schema::drop('configuracoes.db_tutorialetapas');
        Schema::drop('configuracoes.db_tutorial');
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
