<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M28976ItemMenuCancelaDesignacao extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('db_itensmenu')->insert([
            'id_item' => 3000319,
            'descricao' => 'Cancelar Designação',
            'help' => 'Cancelar Designação',
            'funcao' => 'edu4_cancelardesignacao.php',
            'itemativo' => '1',
            'manutencao' => '1',
            'desctec' => 'Cancelamento de designação da transposição',
            'libcliente' => false
        ]);

        DB::table('db_menu')->insert([
            'id_item' => 3000311,
            'id_item_filho' => 3000319,
            'menusequencia' => 6,
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
        DB::table('db_menu')
            ->where('id_item_filho', 3000319)
            ->where('modulo', 7159)
            ->delete();

        DB::table('db_itensmenu')
            ->where('id_item', 3000319)
            ->delete();
    }
}
