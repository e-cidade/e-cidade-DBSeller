<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M26416CriandoMenuCamposOpcionaisEParametros extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $dados = [
            [229001 ,'Campos Opcionais' ,'Campos Opcionais' ,'web/educacao/matricula-online/configuracao/campos-opcionais' ,'1' ,'1' ,'Campos Opcionais' ,'true'],
            [229002 ,'Parâmetros' ,'Parâmetros' ,'web/educacao/matricula-online/configuracao/parametros' ,'1' ,'1' ,'Parâmetros' ,'true']
        ];

        foreach ($dados as $dado) {
            DB::table('configuracoes.db_itensmenu')->insert([
                'id_item' => $dado[0],
                'descricao' => $dado[1],
                'help' => $dado[2],
                'funcao' => $dado[3],
                'itemativo' => $dado[4],
                'manutencao' => $dado[5],
                'desctec' => $dado[6],
                'libcliente' => $dado[7]
            ]);
        }

        $dados = [
            [228978 ,229001 ,7 ,228923],
            [228978 ,229002 ,8 ,228923]
        ];

        foreach ($dados as $dado) {
            DB::table('configuracoes.db_menu')->insert([
                'id_item' => $dado[0],
                'id_item_filho' => $dado[1],
                'menusequencia' => $dado[2],
                'modulo' => $dado[3]
            ]);
        }

        $plugin = DB::table('configuracoes.db_plugin')->where('db145_nome', 'matricula-on-line')->get();
        if ($plugin->count() > 0) {
            $uids = [
                "facil-5d88b16ab2a76",
                "facil-5d88b1c2bec62"
            ];

            $itemId = DB::table('configuracoes.db_pluginitensmenu')->select('db146_db_itensmenu')->whereIn('db146_uid', $uids)->get();
            DB::table('configuracoes.db_pluginitensmenu')->whereIn('db146_uid', $uids)->where('db146_db_plugin', $plugin->first()->db145_sequencial)->delete();

            foreach ($itemId as $it) {
                DB::table('configuracoes.db_itensmenu')->where('id_item', $it->db146_db_itensmenu)->delete();
                DB::table('configuracoes.db_menu')->where('id_item_filho', $it->db146_db_itensmenu)->delete();
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $dados = [229001, 229002];
        DB::table('configuracoes.db_itensmenu')->whereIn('id_item', $dados)->delete();
        DB::table('configuracoes.db_menu')->whereIn('id_item_filho', $dados)->delete();
    }
}
