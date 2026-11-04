<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M22395AlterandoRotaDoItemDeMenoEscolaRelatoriosAlunosHistoricoEscolar extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('configuracoes.db_itensmenu')->where('id_item', 1100955)
            ->update(['funcao' => 'web/educacao/escola/relatorios/alunos/historico-escolar']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('configuracoes.db_itensmenu')->where('id_item', 1100955)
            ->update(['funcao' => 'edu2_historico001.php']);
    }
}
