<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M15201AlterandoFuncaoDoItemDeMenu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('configuracoes.db_itensmenu')->where('id_item', 8663)->update([
            'funcao' => 'web/educacao/escola/relatorios/alunos/ficha-individual-aluno'
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('configuracoes.db_itensmenu')->where('id_item', 8663)->update([
            'funcao' => 'edu2_fichaindividualaluno001.php'
        ]);
    }
}
