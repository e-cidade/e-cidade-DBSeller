<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M23464CriandoItemDeMenuRelatorioAtividades extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table("db_itensmenu")->insert([
            "id_item" => 228965,
            "descricao" => 'Atividades',
            "help" => 'Relatório de Atividades',
            "funcao" => 'web/educacao/escola/relatorios/recursos-humanos/atividades',
            "itemativo" => '1',
            "manutencao" => '1',
            "desctec" => 'Relatório de atividades.',
            "libcliente" => 'true'
        ]);

        $dados = [
            [1101111, 228965, 14, 1100747],
            [1101111, 228965, 15, 7159]
        ];

        foreach ($dados as $dado) {
            DB::table("db_menu")->insert([
                "id_item" => $dado[0],
                "id_item_filho" => $dado[1],
                "menusequencia" => $dado[2],
                "modulo" => $dado[3]
            ]);
        }

        DB::table("db_itensfilho")->where("id_item", 1101085)->delete();
        DB::table("db_menu")->where("id_item_filho", 1101085)->delete();
        DB::table("db_itensmenu")->where("id_item", 1101085)->delete();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table("db_menu")->where("id_item_filho", 228965)->delete();
        DB::table("db_itensmenu")->where("id_item", 228965)->delete();

    }
}
