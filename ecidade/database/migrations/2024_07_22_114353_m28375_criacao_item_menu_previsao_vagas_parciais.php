<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M28375CriacaoItemMenuPrevisaoVagasParciais extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->upDicionario();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->downDicionario();
    }

    private function upDicionario()
    {
        DB::table('db_itensmenu')->insert([
            'id_item' => 229278,
            'descricao' => 'Previsão de Vagas Parciais',
            'help' => 'Previsão de Vagas Parciais',
            'funcao' => 'web/educacao/matricula-online/relatorios/previsao-vagas-parciais',
            'itemativo' => '1',
            'manutencao' => '1',
            'desctec' => 'Emissão do relatório de previsão de vagas parciais.',
            'libcliente' => true
        ]);

        DB::table('db_menu')->insert([
            'id_item' => 228933,
            'id_item_filho' => 229278,
            'menusequencia' => 2,
            'modulo' => 228923
        ]);
    }

    private function downDicionario()
    {
        DB::table('db_menu')
            ->where('id_item_filho', 229278)
            ->where('modulo', 228923)
            ->delete();

        DB::table('db_itensmenu')
            ->where('id_item', 229278)
            ->delete();
    }
}
