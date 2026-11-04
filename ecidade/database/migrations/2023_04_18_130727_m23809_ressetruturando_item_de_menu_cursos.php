<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M23809RessetruturandoItemDeMenuCursos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('configuracoes.db_itensmenu')->where('id_item', 1101210)->update([
            'funcao' => 'web/educacao/secretaria/cadastros/cursos/crud',
            'descricao' => 'Cadastro de Cursos',
            'help' => 'Cadastro de Cursos',
            'desctec' => 'Cadastro de Cursos'
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('configuracoes.db_itensmenu')->where('id_item', 1101210)->update([
            'funcao' => 'edu1_cursoedu001.php',
            'descricao' => 'Inclusão',
            'help' => 'Inclusão',
            'desctec' => 'Inclusão'
        ]);
    }
}
