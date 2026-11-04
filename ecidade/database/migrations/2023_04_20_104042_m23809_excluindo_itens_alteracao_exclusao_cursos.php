<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M23809ExcluindoItensAlteracaoExclusaoCursos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('configuracoes.db_itensfilho')->whereIn('id_item', [1101211, 1101212])->delete();
        DB::table('configuracoes.db_itensmenu')->whereIn('id_item', [1101211, 1101212])->delete();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('configuracoes.db_itensmenu')->insert([
            'id_item' => 1101211,
            'descricao' => 'Alteração',
            'help' => 'Alteração',
            'funcao' => 'edu1_cursoedusec002.php',
            'itemativo' => '1',
            'manutencao' => '1',
            'desctec' => 'Alteração',
            'libcliente' => 'true'
        ]);

        DB::table('configuracoes.db_itensmenu')->insert([
            'id_item' => 1101212,
            'descricao' => 'Exclusão',
            'help' => 'Exclusão',
            'funcao' => 'edu1_cursoedu003.php',
            'itemativo' => '1',
            'manutencao' => '1',
            'desctec' => 'Exclusão',
            'libcliente' => 'true'
        ]);

        DB::table('configuracoes.db_itensfilho')->insert([
            'id_item' => 1101211,
            'codfilho' => 1008320

        ]);

        DB::table('configuracoes.db_itensfilho')->insert([
            'id_item' => 1101212,
            'codfilho' => 1008321
        ]);
    }
}
