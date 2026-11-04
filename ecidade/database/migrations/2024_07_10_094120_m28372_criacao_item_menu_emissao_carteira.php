<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M28372CriacaoItemMenuEmissaoCarteira extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   DB::table('db_itensmenu')->insert([
            'id_item' => 229272,
            'descricao' => 'Emissão de Carteira',
            'help' => 'Emissão de Carteira',
            'funcao' => 'web/educacao/transporte-escolar/relatorios/emissao-carteira',
            'itemativo' => '1',
            'manutencao' => '1',
            'desctec' => 'Rotina pra emissão de carteira para utilização do transporte escolar municipal.',
            'libcliente' => true
        ]);

        DB::table('db_menu')->insert([
           'id_item' => 32,
           'id_item_filho' => 229272,
           'menusequencia' => 585,
           'modulo' => 7147
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {   DB::table('db_menu')
            ->where('id_item_filho', 229272)
            ->where('modulo', 7147)
            ->delete();

        DB::table('db_itensmenu')
            ->where('id_item', 229272)
            ->delete();
    }
}
