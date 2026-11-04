<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M25741AddTipoDespacho extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->addTipoDespacho();
    }

    public function addTipoDespacho() {
        DB::table('protocolo.tipodespacho')->insert([
            'p100_sequencial' => 1004,
            'p100_descricao' => 'Origem Mensagem',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->downTipoDespacho();
    }

    public function downTipoDespacho()
    {
        DB::table('protocolo.tipodespacho')
            ->where('p100_sequencial', '=', 1004)
            ->delete();
    }
}
