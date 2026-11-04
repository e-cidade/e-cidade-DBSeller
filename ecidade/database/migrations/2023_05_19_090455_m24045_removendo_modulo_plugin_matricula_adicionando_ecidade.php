<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M24045RemovendoModuloPluginMatriculaAdicionandoEcidade extends Migration
{
    public $modulo;
    public $plugin;
    public $atendcadareamod;
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->upRemovendoEstruturaPlugin();
        $this->upNovaEstruturaDicionario();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->downNovaEstruturaDicionario();
    }

    public function upRemovendoEstruturaPlugin()
    {
        $this->modulo = $this->getModulo();
        $this->plugin = $this->getPlugin();
        $this->atendcadareamod = $this->getAtendcadareamod();

        if (!is_null($this->modulo)) {
            // removendo modulo Matriula Online
            DB::table('configuracoes.db_pluginmodulos')->where('db152_uid', 'facil-37e7-a913-3b422475cce9')->delete();
            DB::table('configuracoes.atendcadareamod')->where('at26_id_item', $this->modulo)->delete();
            DB::table('configuracoes.db_modulos')->where('id_item', $this->modulo)->delete();

//------------------------------------------------------------------------------------------------------------------//

            // remvoendo item de menu Cadastros, consultas, relatorios, procedimentos
            DB::table('configuracoes.db_menu')->where('id_item', $this->modulo)->where('id_item_filho', 29)->delete();
            DB::table('configuracoes.db_menu')->where('id_item', $this->modulo)->where('id_item_filho', 30)->delete();
            DB::table('configuracoes.db_menu')->where('id_item', $this->modulo)->where('id_item_filho', 31)->delete();
            DB::table('configuracoes.db_menu')->where('id_item', $this->modulo)->where('id_item_filho', 32)->delete();
        }


        if (!is_null($this->plugin)) {

            //------------------------------------------------------------------------------------------------------------------//
            //removendo todos itens do plugin
            $ids = DB::table('configuracoes.db_pluginitensmenu')->where('db146_db_plugin', $this->plugin)->get()
                ->map(function ($item) {
                    return $item->db146_db_itensmenu;
                });
            if (count($ids) > 0) {
                DB::table('configuracoes.db_pluginitensmenu')->where('db146_db_plugin', $this->plugin)->delete();
                DB::table('configuracoes.db_menu')->whereIn('id_item', $ids)->delete();
            }
        }

    }

    public function upNovaEstruturaDicionario()
    {
        $dados = [
            [228923 ,'Matrícula On Line' ,'Matrícula On Line' ,'' ,'2' ,'1' ,'Matrícula On Line' ,'true'],

            [228924 ,'Cadastros' ,'Cadastros' ,'' ,'1' ,'1' ,'Cadastros Modulo Matricula On Line' ,'true'],
            [228933 ,'Relatórios' ,'Relatórios' ,'' ,'1' ,'1' ,'Relatórios Matricula On-Line' ,'true'],
            [228934 ,'Consultas' ,'Consultas' ,'' ,'1' ,'1' ,'Consultas Matrícula On-Line' ,'true'],
            [228935 ,'Procedimentos' ,'Procedimentos' ,'' ,'1' ,'1' ,'Procedimentos Matrícula On-Line' ,'true'],

            [228925 ,'Fases' ,'Fases' ,'web/educacao/matricula-online/cadastros/fases' ,'1' ,'1' ,'Cadastro de Fases' ,'true'],
            [228930 ,'Ciclos' ,'Ciclos' ,'web/educacao/matricula-online/cadastros/ciclos' ,'1' ,'1' ,'Ciclos' ,'true']
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

        DB::table('configuracoes.db_modulos')->insert([
            'id_item' => 228923,
            'nome_modulo' => 'Matrícula On Line',
            'descr_modulo' => 'Matrícula On Line',
            'imagem' => null,
            'temexerc' => true
        ]);


        DB::table('configuracoes.atendcadareamod')->insert([
            'at26_sequencia' => 87,
            'at26_codarea' => 8,
            'at26_id_item' => 228923
        ]);


        $dados = [
            [228923, 228924, 1, 228923],
            [228924, 228925, 1, 228923],
            [228924, 228930, 1, 228923],
            [228923 ,228933 ,2 ,228923],
            [228923 ,228934 ,3 ,228923],
            [228923 ,228935 ,4 ,228923]
        ];

        foreach ($dados as $dado) {
            DB::table('configuracoes.db_menu')->insert([
                'id_item' => $dado[0],
                'id_item_filho' => $dado[1],
                'menusequencia' => $dado[2],
                'modulo' => $dado[3]
            ]);
        }
    }
    public function downNovaEstruturaDicionario()
    {
        $itens = [228923, 228924, 228925, 228930, 228933, 228934, 228935];
        DB::table('configuracoes.db_menu')->where('modulo', 228923)->delete();
        DB::table('configuracoes.atendcadareamod')->where('at26_sequencia', 87)->delete();
        DB::table('configuracoes.db_modulos')->where('id_item', 228923)->delete();
        DB::table('configuracoes.db_itensmenu')->whereIn('id_item', $itens)->delete();
    }


    public function getModulo()
    {
        $clt = DB::table('configuracoes.db_pluginmodulos')->where('db152_uid', 'facil-37e7-a913-3b422475cce9')->get();
        return $clt->count() > 0 ? $clt->first()->db152_db_modulo : null;
    }


    /**
     * @return mixed
     */
    public function getPlugin()
    {
        $plugin = DB::table('configuracoes.db_plugin')->where('db145_nome', 'matricula-on-line')->get()
            ->map(function ($item) {
                return $item->db145_sequencial;
            });

        return $plugin->count() > 0 ? $plugin : null;
    }

    /**
     * @return mixed
     */
    public function getAtendcadareamod()
    {
        return DB::table('configuracoes.atendcadareamod')->where('at26_id_item', $this->modulo)->get()
            ->map(function ($item) {
                return $item->at26_sequencia;
            });
    }
}
