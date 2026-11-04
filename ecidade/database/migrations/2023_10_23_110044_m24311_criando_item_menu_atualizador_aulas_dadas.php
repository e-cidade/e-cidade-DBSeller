<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M24311CriandoItemMenuAtualizadorAulasDadas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('configuracoes.db_itensmenu')->insert([
            'id_item' => 228984,
            'descricao' => 'Atualizador Aulas Dadas',
            'help' => 'Atualizador Aulas Dadas',
            'funcao' => 'web/educacao/escola/procedimentos/diario-classe/atualizador-aulas-dadas',
            'itemativo' => '1',
            'manutencao' => '1',
            'desctec' => 'Atualizador Aulas Dadas',
            'libcliente' => 'true'
        ]);

        DB::table('configuracoes.db_menu')->insert([
            'id_item' => 1100930,
            'id_item_filho' => 228984,
            'menusequencia' => 9,
            'modulo' => 1100747
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('configuracoes.db_itensmenu')->where('id_item', '=', 228984)->delete();
        DB::table('configuracoes.db_menu')->where('id_item_filho', '=', 228984)->delete();
    }
}
