<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M23798CriandoItemDeMenuAlunosEstrnageiros extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('configuracoes.db_itensmenu')->insert([
            'id_item' => 228870,
            'descricao' => 'Alunos Estrangeiros',
            'help' => 'Alunos Estrangeiros',
            'funcao' => 'web/educacao/secretaria/relatorios/alunos/alunos-estrangeiros',
            'itemativo' => '1',
            'manutencao' => '1',
            'desctec' => 'Relatório de Alunos Estrangeiros',
            'libcliente' => 'true'
        ]);

        DB::table('configuracoes.db_menu')->insert([
            'id_item' => 1101109,
            'id_item_filho' => 228870,
            'menusequencia' => 27,
            'modulo' => 7159
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('configuracoes.db_itensmenu')->where('id_item', '=', 228870)->delete();
        DB::table('configuracoes.db_menu')->where('id_item_filho', '=', 228870)->delete();
    }
}
