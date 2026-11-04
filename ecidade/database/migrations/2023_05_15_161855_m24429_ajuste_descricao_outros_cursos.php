<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M24429AjusteDescricaoOutrosCursos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('habitacao.avaliacaoperguntaopcao')->where('db104_sequencial', 3000119)->update([
            'db104_descricao' => 'Educação para as relações étnico-raciais e História e cultura afro-brasileira e africana'
        ]);
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
