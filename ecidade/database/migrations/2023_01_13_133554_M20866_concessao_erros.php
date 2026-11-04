<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M20866ConcessaoErros extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recursoshumanos.concessao_direitos_erros', function (Blueprint $table) {
            $table->bigIncrements('rh509_sequencial');
            $table->integer('rh509_matricula')->nullable();
            $table->text('rh509_erro');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('recursoshumanos.concessao_direitos_erros');
    }
}
