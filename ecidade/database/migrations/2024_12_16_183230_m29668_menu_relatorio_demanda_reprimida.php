<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M29668MenuRelatorioDemandaReprimida extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->criaItemMenu();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->dropaItemMenu();
    }

    private function criaItemMenu()
    {

        DB::table('db_itensmenu')->insert([
            'id_item' => 229366,
            'descricao' => 'Demanda Reprimida',
            'help' => 'Demanda Reprimida',
            'funcao' => 'web/educacao/matricula-online/relatorios/demanda-reprimida',
            'itemativo' => '1',
            'manutencao' => '1',
            'desctec' => 'Relatório que irá exibir informações sobre candidatura e situação da matrícula.',
            'libcliente' => true,
            'api' => false
        ]);

        DB::table('db_menu')->insert([
            'id_item' => 228933,
            'id_item_filho' => 229366,
            'menusequencia' => 4,
            'modulo' => 228923
        ]);
    }

    private function dropaItemMenu()
    {
        DB::table('db_menu')
            ->where('id_item_filho', 229366)
            ->where('modulo', 228923)
            ->delete();

        DB::table('db_itensmenu')
            ->where('id_item', 229366)
            ->delete();
    }
}
