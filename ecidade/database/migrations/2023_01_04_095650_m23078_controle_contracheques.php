<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M23078ControleContracheques extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contracheques_batches', function (Blueprint $table) {
            $table->bigIncrements('rh269_codigo');
            $table->bigInteger('rh269_instit');
            $table->bigInteger('rh269_batch');
            $table->string('rh269_competencia');
            $table->integer('rh269_total');

            $table->foreign('rh269_instit', 'contracheques_batches_rh269_instit_fk')
                ->references('codigo')
                ->on('db_config');

            $table->foreign('rh269_batch', 'contracheques_batches_rh269_batch_fk')
                ->references('id')
                ->on('batch_jobs');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('contracheques_batches');
    }
}
