<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M23524AlterandoFuncaoDoItemDeMenu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('configuracoes.db_itensmenu')->where('id_item', 7011)->update([
            'funcao' => 'web/educacao/escola/relatorios/alunos/atestado-frequencia'
        ]);
    }

    /**
     * Reverse the migrations.
     **
     * @return void
     */
    public function down()
    {
        DB::table('configuracoes.db_itensmenu')->where('id_item', 7011)->update([
            'funcao' => 'edu2_atestadofrequencia001.php'
        ]);
    }
}
