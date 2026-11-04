<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M27069AtualizarSequenciaAcordoparam extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
            update db_syssequencia set nomesequencia = 'acordoparam_ac59_sequencial_seq' where codsequencia = 1000995;
            alter sequence homologacaoacordo_ac59_sequencial_seq rename to acordoparam_ac59_sequencial_seq
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
        DB::connection()->getPdo()->exec(<<<SQL
            update db_syssequencia set nomesequencia = 'homologacaoacordo_ac59_sequencial_seq' where codsequencia = 1000995;
            alter sequence acordoparam_ac59_sequencial_seq rename to homologacaoacordo_ac59_sequencial_seq
SQL
        );
    }
}
